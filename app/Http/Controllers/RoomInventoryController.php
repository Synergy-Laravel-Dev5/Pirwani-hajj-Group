<?php

namespace App\Http\Controllers;

use App\Models\RoomInventory;
use App\Models\Hotel;
use App\Models\Company;
use App\Models\BookingHotel;
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

        // 1. Calculate Room Type Summary Breakdown
        $summary = RoomInventory::getHotelSummary($hotelId, $checkIn, $checkOut);

        // Overall Totals
        $totalStock = array_sum(array_column($summary, 'total_stock'));
        $totalBooked = array_sum(array_column($summary, 'booked'));
        $totalAvailable = array_sum(array_column($summary, 'available'));
        $overallOccupancy = $totalStock > 0 ? min(100, round(($totalBooked / $totalStock) * 100)) : 0;

        // 2. Fetch Rooming List (Booked hotels in bookings)
        $roomingListQuery = BookingHotel::with([
            'booking.client',
            'booking.company',
            'booking.persons'
        ])->whereHas('booking', function ($q) {
            $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
        });

        if (isCompanyUser()) {
            $roomingListQuery->whereHas('booking', function ($bq) {
                $bq->where('company_id', getAuthCompanyId());
            });
        }

        if ($hotelId) {
            $hotel = Hotel::find($hotelId);
            if ($hotel) {
                $roomingListQuery->where('hotel_name', 'like', "%{$hotel->name}%");
            }
        }

        if ($city) {
            $roomingListQuery->where(function ($q) use ($city) {
                $q->where('location', 'like', "%{$city}%")
                  ->orWhere('hotel_name', 'like', "%{$city}%");
            });
        }

        if ($roomType) {
            $roomingListQuery->whereRaw('LOWER(TRIM(room_type)) = ?', [strtolower($roomType)]);
        }

        if ($checkIn && $checkOut) {
            $cIn = date('Y-m-d', strtotime($checkIn));
            $cOut = date('Y-m-d', strtotime($checkOut));
            $roomingListQuery->where(function ($q) use ($cIn, $cOut) {
                $q->where('check_in', '<=', $cOut)->where('check_out', '>=', $cIn);
            });
        }

        $roomingList = $roomingListQuery->latest()->get();

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
            'summary',
            'totalStock',
            'totalBooked',
            'totalAvailable',
            'overallOccupancy',
            'roomingList',
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
        $roomTypes = ['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Sharing', 'Suite'];

        return view('room_inventory.create', compact('hotels', 'companies', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
        ]);

        DB::beginTransaction();
        try {
            $hotelId = $request->hotel_id;
            $batchName = $request->batch_name ?: 'Stock Allotment ' . date('d-M-Y');
            $checkIn = !empty($request->check_in) ? date('Y-m-d', strtotime($request->check_in)) : null;
            $checkOut = !empty($request->check_out) ? date('Y-m-d', strtotime($request->check_out)) : null;
            $supplierId = $request->supplier_id ?: null;
            $currency = $request->currency ?: 'SAR';
            $notes = $request->notes ?: null;

            $roomCounts = $request->input('rooms', []);

            if (empty($roomCounts)) {
                $types = ['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Sharing', 'Suite'];
                foreach ($types as $t) {
                    $field = strtolower($t) . '_rooms';
                    if ($request->filled($field) && intval($request->$field) > 0) {
                        $roomCounts[$t] = intval($request->$field);
                    }
                }
            }

            if (empty($roomCounts) && $request->filled('room_type') && $request->filled('total_rooms')) {
                $roomCounts[$request->room_type] = intval($request->total_rooms);
            }

            if (empty($roomCounts)) {
                return back()->withInput()->withErrors(['error' => 'Please enter at least one room quantity (e.g. 10 Double, 5 Triple, etc.).']);
            }

            $createdCount = 0;
            foreach ($roomCounts as $roomType => $quantity) {
                $qty = intval($quantity);
                $isSharing = (strtolower(trim($roomType)) === 'sharing');

                $maleBeds = 0;
                $femaleBeds = 0;
                $totalBeds = 0;

                if ($isSharing) {
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

                if ($qty <= 0 && (!$isSharing || ($maleBeds + $femaleBeds + $totalBeds) <= 0)) {
                    continue;
                }

                if ($isSharing && $qty <= 0) {
                    $qty = max(1, $totalBeds, ($maleBeds + $femaleBeds));
                }

                $costRate = floatval($request->input("cost_rate_{$roomType}", $request->cost_rate ?? 0));
                $sellingRate = floatval($request->input("selling_rate_{$roomType}", $request->selling_rate ?? 0));
                $roomView = $request->input("room_view_{$roomType}", $request->room_view ?? 'City View');
                $mealPlan = $request->input("meal_plan_{$roomType}", $request->meal_plan ?? 'Room Only');

                RoomInventory::create([
                    'hotel_id'     => $hotelId,
                    'batch_name'   => $batchName,
                    'room_type'    => $roomType,
                    'room_view'    => $roomView,
                    'meal_plan'    => $mealPlan,
                    'check_in'     => $checkIn,
                    'check_out'    => $checkOut,
                    'total_rooms'  => $qty,
                    'male_beds'    => $maleBeds,
                    'female_beds'  => $femaleBeds,
                    'total_beds'   => $totalBeds ?: $qty,
                    'cost_rate'    => $costRate,
                    'selling_rate' => $sellingRate,
                    'currency'     => $currency,
                    'supplier_id'  => $supplierId,
                    'status'       => 'active',
                    'notes'        => $notes,
                ]);

                $createdCount++;
            }

            DB::commit();

            return redirect()
                ->route('room-inventory.index')
                ->with('success', "Room inventory added successfully! ({$createdCount} room types provisioned)");

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
        $roomTypes = ['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Sharing', 'Suite'];

        return view('room_inventory.edit', compact('inventory', 'hotels', 'companies', 'roomTypes'));
    }

    public function update(Request $request, $id)
    {
        $inventory = RoomInventory::findOrFail($id);

        $request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'room_type'   => 'required|string',
            'total_rooms' => 'required|integer|min:0',
        ]);

        $maleBeds = intval($request->input('male_beds', $inventory->male_beds ?? 0));
        $femaleBeds = intval($request->input('female_beds', $inventory->female_beds ?? 0));
        $totalBeds = intval($request->input('total_beds', $inventory->total_beds ?? 0));
        if (($maleBeds + $femaleBeds) > 0 && $totalBeds <= 0) {
            $totalBeds = $maleBeds + $femaleBeds;
        }

        $inventory->update([
            'hotel_id'     => $request->hotel_id,
            'batch_name'   => $request->batch_name ?: $inventory->batch_name,
            'room_type'    => $request->room_type,
            'room_view'    => $request->room_view ?: 'City View',
            'meal_plan'    => $request->meal_plan ?: 'Room Only',
            'check_in'     => !empty($request->check_in) ? date('Y-m-d', strtotime($request->check_in)) : null,
            'check_out'    => !empty($request->check_out) ? date('Y-m-d', strtotime($request->check_out)) : null,
            'total_rooms'  => intval($request->total_rooms),
            'male_beds'    => $maleBeds,
            'female_beds'  => $femaleBeds,
            'total_beds'   => $totalBeds,
            'cost_rate'    => floatval($request->cost_rate ?? 0),
            'selling_rate' => floatval($request->selling_rate ?? 0),
            'currency'     => $request->currency ?: 'SAR',
            'supplier_id'  => $request->supplier_id ?: null,
            'status'       => $request->status ?: 'active',
            'notes'        => $request->notes ?: null,
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
