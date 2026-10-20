<?php

namespace App\Http\Controllers;

use App\Models\RoomInventory;
use App\Models\Hotel;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\BookingHotel;
use App\Models\HotelSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomInventoryController extends Controller
{
    public function index(Request $request)
    {
        $hotelId = $request->get('hotel_id');
        $city = $request->get('city');
        $roomType = $request->get('room_type');
        $checkIn = $request->get('check_in');
        $checkOut = $request->get('check_out');

        $hotels = Hotel::where('status', 'active')->orWhereNull('status')->orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();

        // 1. Calculate Room Type Summary Breakdown
        $summary = RoomInventory::getHotelSummary($hotelId, $checkIn, $checkOut);

        // Overall Totals separated for Rooms and Beds
        $roomSummary = array_filter($summary, fn($s) => empty($s['is_bed']));
        $bedSummary = array_filter($summary, fn($s) => !empty($s['is_bed']));

        $totalRoomStock = array_sum(array_column($roomSummary, 'total_stock'));
        $totalRoomBooked = array_sum(array_column($roomSummary, 'booked'));
        $totalRoomAvailable = array_sum(array_column($roomSummary, 'available'));

        $totalBedStock = array_sum(array_column($bedSummary, 'total_stock'));
        $totalBedBooked = array_sum(array_column($bedSummary, 'booked'));
        $totalBedAvailable = array_sum(array_column($bedSummary, 'available'));

        $totalStock = $totalRoomStock + $totalBedStock;
        $totalBooked = $totalRoomBooked + $totalBedBooked;
        $totalAvailable = $totalRoomAvailable + $totalBedAvailable;
        $overallOccupancy = $totalStock > 0 ? min(100, round(($totalBooked / $totalStock) * 100)) : 0;

        // 2. Fetch Sold Hotels / Client & Agent Hotel Sales (Strictly from Hotel Sale Invoices)
        $soldItemsQuery = HotelSaleItem::with([
            'hotelSale.company',
            'hotelSale.client',
            'hotel'
        ])->whereHas('hotelSale', function ($q) {
            $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
        });

        if ($hotelId) {
            $hotel = Hotel::find($hotelId);
            if ($hotel) {
                $soldItemsQuery->where(function ($q) use ($hotelId, $hotel) {
                    $q->where('hotel_id', $hotelId)
                      ->orWhere('hotel_name', 'like', "%{$hotel->name}%");
                });
            }
        }

        if ($city) {
            $soldItemsQuery->whereHas('hotel', function ($q) use ($city) {
                $q->where('city', $city)->orWhere('place', $city);
            });
        }

        if ($roomType) {
            if (strtolower($roomType) === 'bed') {
                $soldItemsQuery->where(function ($q) {
                    $q->where('allotment_basis', 'bed')
                      ->orWhereRaw('LOWER(TRIM(room_type)) = ?', ['bed']);
                });
            } else {
                $soldItemsQuery->whereRaw('LOWER(TRIM(room_type)) = ?', [strtolower($roomType)])
                    ->where(function ($q) {
                        $q->whereNull('allotment_basis')->orWhere('allotment_basis', '!=', 'bed');
                    });
            }
        }

        if ($checkIn && $checkOut) {
            $cIn = date('Y-m-d', strtotime($checkIn));
            $cOut = date('Y-m-d', strtotime($checkOut));
            $soldItemsQuery->where(function ($q) use ($cIn, $cOut) {
                $q->where('check_in', '<=', $cOut)->where('check_out', '>=', $cIn);
            });
        }

        $soldItems = $soldItemsQuery->latest()->get();

        // 3. Fetch Configured Inventory Stock Records
        $inventoryQuery = RoomInventory::with(['hotel', 'supplier'])->latest();

        if ($hotelId) {
            $inventoryQuery->where('hotel_id', $hotelId);
        }

        if ($roomType) {
            $inventoryQuery->whereRaw('LOWER(TRIM(room_type)) = ?', [strtolower($roomType)]);
        }

        if ($city) {
            $inventoryQuery->whereHas('hotel', function ($q) use ($city) {
                $q->where('city', $city)->orWhere('place', $city);
            });
        }

        $inventories = $inventoryQuery->get();

        return view('room_inventory.index', compact(
            'hotels',
            'companies',
            'suppliers',
            'summary',
            'totalStock',
            'totalBooked',
            'totalAvailable',
            'totalRoomStock',
            'totalRoomBooked',
            'totalRoomAvailable',
            'totalBedStock',
            'totalBedBooked',
            'totalBedAvailable',
            'overallOccupancy',
            'soldItems',
            'inventories',
            'hotelId',
            'city',
            'roomType',
            'checkIn',
            'checkOut'
        ));
    }

    public function create()
    {
        $hotels = Hotel::where('status', 'active')->orWhereNull('status')->orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $roomTypes = \App\Models\RoomType::where('status', 'active')->orderBy('capacity')->pluck('name')->toArray();
        if (empty($roomTypes)) {
            $roomTypes = ['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Bed', 'Sharing', 'Suite'];
        } elseif (!in_array('Bed', $roomTypes)) {
            $roomTypes[] = 'Bed';
        }

        return view('room_inventory.create', compact('hotels', 'companies', 'suppliers', 'roomTypes'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $createdCount = 0;

            // Scenario A: Multi-row / Itemized Allotment entries (Contract sheet style)
            if ($request->has('allotments') && is_array($request->allotments) && count($request->allotments) > 0) {
                $batchName = $request->batch_name ?: ('Hajj Allotment ' . date('Y'));
                $currency = $request->currency ?: 'SAR';
                $supplierId = $request->supplier_id ?: null;
                $defaultNotes = $request->notes ?: null;

                foreach ($request->allotments as $row) {
                    $hotelId = $row['hotel_id'] ?? $request->hotel_id;
                    if (empty($hotelId)) {
                        continue;
                    }

                    $basis = $row['allotment_basis'] ?? 'room';
                    $type = strtoupper(trim($row['room_type'] ?? ($basis === 'bed' ? 'BED' : 'DOUBLE')));
                    $qty = intval($row['quantity'] ?? 0);

                    if ($qty <= 0) {
                        continue;
                    }

                    $nights = max(1, intval($row['no_of_nights'] ?? 1));
                    $costRate = floatval($row['cost_rate'] ?? 0);
                    $sellingRate = floatval($row['selling_rate'] ?? 0);
                    $totalCost = floatval($row['total_cost'] ?? ($qty * $nights * $costRate));
                    $totalSelling = floatval($row['total_selling'] ?? ($qty * $nights * $sellingRate));
                    $mealPlan = $row['meal_plan'] ?? 'Room Only';
                    $roomView = $row['room_view'] ?? 'City View';

                    $checkIn = !empty($row['check_in']) ? date('Y-m-d', strtotime($row['check_in'])) : null;
                    $checkOut = !empty($row['check_out']) ? date('Y-m-d', strtotime($row['check_out'])) : null;
                    $hijriCheckIn = !empty($row['hijri_check_in']) ? trim($row['hijri_check_in']) : null;
                    $hijriCheckOut = !empty($row['hijri_check_out']) ? trim($row['hijri_check_out']) : null;

                    $maleBeds = intval($row['male_beds'] ?? 0);
                    $femaleBeds = intval($row['female_beds'] ?? 0);
                    $totalBeds = ($basis === 'bed' || $type === 'BED') ? $qty : (($maleBeds + $femaleBeds) ?: $qty);

                    RoomInventory::create([
                        'hotel_id'        => $hotelId,
                        'batch_name'      => $batchName,
                        'allotment_basis' => $basis,
                        'room_type'       => $type,
                        'room_view'       => $roomView,
                        'meal_plan'       => $mealPlan,
                        'check_in'        => $checkIn,
                        'check_out'       => $checkOut,
                        'hijri_check_in'  => $hijriCheckIn,
                        'hijri_check_out' => $hijriCheckOut,
                        'no_of_nights'    => $nights,
                        'total_rooms'     => ($basis === 'room') ? $qty : 0,
                        'male_beds'       => $maleBeds,
                        'female_beds'     => $femaleBeds,
                        'total_beds'      => $totalBeds,
                        'cost_rate'       => $costRate,
                        'selling_rate'    => $sellingRate,
                        'total_cost'      => $totalCost,
                        'total_selling'   => $totalSelling,
                        'currency'        => $currency,
                        'supplier_id'     => $supplierId,
                        'status'          => 'active',
                        'notes'           => $row['notes'] ?? $defaultNotes,
                    ]);

                    $createdCount++;
                }

                if ($createdCount === 0) {
                    return back()->withInput()->withErrors(['error' => 'Please enter at least one valid allotment row with quantity > 0.']);
                }

            } else {
                // Scenario B: Form with roomCounts & dedicated Bed input
                $request->validate([
                    'hotel_id' => 'required|exists:hotels,id',
                ]);

                $hotelId = $request->hotel_id;
                $batchName = $request->batch_name ?: ('Stock Allotment ' . date('d-M-Y'));
                $checkIn = !empty($request->check_in) ? date('Y-m-d', strtotime($request->check_in)) : null;
                $checkOut = !empty($request->check_out) ? date('Y-m-d', strtotime($request->check_out)) : null;
                $hijriCheckIn = $request->hijri_check_in ?: null;
                $hijriCheckOut = $request->hijri_check_out ?: null;
                $nights = max(1, intval($request->no_of_nights ?? 1));
                $supplierId = $request->supplier_id ?: null;
                $currency = $request->currency ?: 'SAR';
                $notes = $request->notes ?: null;

                $roomCounts = $request->input('rooms', []);

                if (empty($roomCounts)) {
                    $types = ['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Bed', 'Sharing', 'Suite'];
                    foreach ($types as $t) {
                        $field = strtolower($t) . '_rooms';
                        if ($request->filled($field) && intval($request->$field) > 0) {
                            $roomCounts[$t] = intval($request->$field);
                        }
                    }
                }

                // Check Bed Stock Allotment input
                if ($request->filled('bed_stock_qty') && intval($request->bed_stock_qty) > 0) {
                    $roomCounts['Bed'] = intval($request->bed_stock_qty);
                }

                if (empty($roomCounts) && $request->filled('room_type') && $request->filled('total_rooms')) {
                    $roomCounts[$request->room_type] = intval($request->total_rooms);
                }

                if (empty($roomCounts)) {
                    return back()->withInput()->withErrors(['error' => 'Please enter at least one room or bed quantity.']);
                }

                foreach ($roomCounts as $roomType => $quantity) {
                    $qty = intval($quantity);
                    $isBed = (strtolower(trim($roomType)) === 'bed');
                    $isSharing = (strtolower(trim($roomType)) === 'sharing');

                    $maleBeds = 0;
                    $femaleBeds = 0;
                    $totalBeds = 0;

                    if ($isBed) {
                        $maleBeds = intval($request->input('bed_male_beds', 0));
                        $femaleBeds = intval($request->input('bed_female_beds', 0));
                        $totalBeds = $qty;
                    } elseif ($isSharing) {
                        $maleBeds = intval($request->input('sharing_male_beds', 0));
                        $femaleBeds = intval($request->input('sharing_female_beds', 0));
                        $totalBeds = intval($request->input('sharing_total_beds', $qty));
                        if (($maleBeds + $femaleBeds) > 0 && $totalBeds <= 0) {
                            $totalBeds = $maleBeds + $femaleBeds;
                        }
                        if ($totalBeds > 0 && $qty <= 0) {
                            $qty = $totalBeds;
                        }
                    }

                    if ($qty <= 0) {
                        continue;
                    }

                    $costRate = floatval($request->input("cost_rate_{$roomType}", $request->cost_rate ?? 0));
                    $sellingRate = floatval($request->input("selling_rate_{$roomType}", $request->selling_rate ?? 0));
                    $roomView = $request->input("room_view_{$roomType}", $request->room_view ?? 'City View');
                    $mealPlan = $request->input("meal_plan_{$roomType}", $request->meal_plan ?? 'Room Only');
                    $totalCost = $qty * $nights * $costRate;
                    $totalSelling = $qty * $nights * $sellingRate;

                    RoomInventory::create([
                        'hotel_id'        => $hotelId,
                        'batch_name'      => $batchName,
                        'allotment_basis' => $isBed ? 'bed' : 'room',
                        'room_type'       => $roomType,
                        'room_view'       => $roomView,
                        'meal_plan'       => $mealPlan,
                        'check_in'        => $checkIn,
                        'check_out'       => $checkOut,
                        'hijri_check_in'  => $hijriCheckIn,
                        'hijri_check_out' => $hijriCheckOut,
                        'no_of_nights'    => $nights,
                        'total_rooms'     => $isBed ? 0 : $qty,
                        'male_beds'       => $maleBeds,
                        'female_beds'     => $femaleBeds,
                        'total_beds'      => $totalBeds ?: $qty,
                        'cost_rate'       => $costRate,
                        'selling_rate'    => $sellingRate,
                        'total_cost'      => $totalCost,
                        'total_selling'   => $totalSelling,
                        'currency'        => $currency,
                        'supplier_id'     => $supplierId,
                        'status'          => 'active',
                        'notes'           => $notes,
                    ]);

                    $createdCount++;
                }
            }

            DB::commit();

            return redirect()
                ->route('room-inventory.index')
                ->with('success', "Room & Bed stock inventory added successfully! ({$createdCount} stock items recorded)");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to save room inventory: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $inventory = RoomInventory::findOrFail($id);
        $hotels = Hotel::where('status', 'active')->orWhereNull('status')->orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $roomTypes = \App\Models\RoomType::where('status', 'active')->orderBy('capacity')->pluck('name')->toArray();
        if (empty($roomTypes)) {
            $roomTypes = ['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Bed', 'Sharing', 'Suite'];
        } elseif (!in_array('Bed', $roomTypes)) {
            $roomTypes[] = 'Bed';
        }

        return view('room_inventory.edit', compact('inventory', 'hotels', 'companies', 'suppliers', 'roomTypes'));
    }

    public function update(Request $request, $id)
    {
        $inventory = RoomInventory::findOrFail($id);

        $request->validate([
            'hotel_id'  => 'required|exists:hotels,id',
            'room_type' => 'required|string',
        ]);

        $basis = $request->input('allotment_basis', $inventory->allotment_basis ?: 'room');
        $qty = intval($request->input('total_rooms', $inventory->total_rooms));
        if ($basis === 'bed' && $qty <= 0) {
            $qty = intval($request->input('total_beds', $inventory->total_beds));
        }

        $nights = max(1, intval($request->input('no_of_nights', $inventory->no_of_nights ?: 1)));
        $costRate = floatval($request->input('cost_rate', $inventory->cost_rate ?? 0));
        $sellingRate = floatval($request->input('selling_rate', $inventory->selling_rate ?? 0));
        $totalCost = $qty * $nights * $costRate;
        $totalSelling = $qty * $nights * $sellingRate;

        $maleBeds = intval($request->input('male_beds', $inventory->male_beds ?? 0));
        $femaleBeds = intval($request->input('female_beds', $inventory->female_beds ?? 0));
        $totalBeds = intval($request->input('total_beds', $inventory->total_beds ?? 0));
        if ($basis === 'bed') {
            $totalBeds = $qty;
        }

        $inventory->update([
            'hotel_id'        => $request->hotel_id,
            'batch_name'      => $request->batch_name ?: $inventory->batch_name,
            'allotment_basis' => $basis,
            'room_type'       => $request->room_type,
            'room_view'       => $request->room_view ?: 'City View',
            'meal_plan'       => $request->meal_plan ?: 'Room Only',
            'check_in'        => !empty($request->check_in) ? date('Y-m-d', strtotime($request->check_in)) : null,
            'check_out'       => !empty($request->check_out) ? date('Y-m-d', strtotime($request->check_out)) : null,
            'hijri_check_in'  => $request->hijri_check_in ?: null,
            'hijri_check_out' => $request->hijri_check_out ?: null,
            'no_of_nights'    => $nights,
            'total_rooms'     => ($basis === 'room') ? $qty : 0,
            'male_beds'       => $maleBeds,
            'female_beds'     => $femaleBeds,
            'total_beds'      => $totalBeds ?: $qty,
            'cost_rate'       => $costRate,
            'selling_rate'    => $sellingRate,
            'total_cost'      => $totalCost,
            'total_selling'   => $totalSelling,
            'currency'        => $request->currency ?: 'SAR',
            'supplier_id'     => $request->supplier_id ?: null,
            'status'          => $request->status ?: 'active',
            'notes'           => $request->notes ?: null,
        ]);

        return redirect()
            ->route('room-inventory.index')
            ->with('success', 'Room inventory allotment updated successfully!');
    }

    public function destroy($id)
    {
        $inventory = RoomInventory::findOrFail($id);
        $inventory->delete();

        return redirect()
            ->route('room-inventory.index')
            ->with('success', 'Room inventory record deleted successfully.');
    }
}
