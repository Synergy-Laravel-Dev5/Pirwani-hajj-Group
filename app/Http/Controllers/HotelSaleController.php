<?php

namespace App\Http\Controllers;

use App\Models\HotelSale;
use App\Models\HotelSaleItem;
use App\Models\Hotel;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = HotelSale::with(['supplier', 'company', 'client', 'items.hotel'])->latest();

        if ($request->filled('sale_for')) {
            $query->where('sale_for', $request->sale_for);
        }

        if ($request->filled('supplier_id')) {
            $supId = $request->supplier_id;
            $query->where(function ($q) use ($supId) {
                $q->where('supplier_id', $supId)
                  ->orWhere('company_id', $supId);
            });
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('reservation_title', 'like', "%{$s}%");
            });
        }

        $sales = $query->paginate(20)->withQueryString();
        $companies = Company::orderBy('company_name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();

        // High level KPI metrics
        $totalSalesCount = HotelSale::count();
        $totalSalesAmount = HotelSale::sum('total_amount');
        $totalTillReceived = HotelSale::sum('till_received');
        $totalBalanceRemaining = HotelSale::sum('balance_remaining');

        return view('hotel_sales.index', compact(
            'sales',
            'companies',
            'suppliers',
            'clients',
            'totalSalesCount',
            'totalSalesAmount',
            'totalTillReceived',
            'totalBalanceRemaining'
        ));
    }

    public function create()
    {
        $hotels = Hotel::where('status', 'active')->orWhereNull('status')->orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $roomTypes = RoomType::where('status', 'active')->orderBy('capacity')->get();

        $nextInvoiceNumber = HotelSale::generateInvoiceNumber();

        // Standard Hajj & Umrah Terms & Conditions from actual contract
        $defaultTerms = "v  Rates are quoted in Saudi Riyal per Bed / Room\n" .
                        "v  Rates are Net & Non Commissionable\n" .
                        "v  Confirmed reservations are non-refundable\n" .
                        "v  15 % VAT & 15 % Muncipalty tax include of above rates\n" .
                        "v  Any other tax if apply will be added your side.";

        $defaultCancellation = "v  100 % charge Applicable once confirmed No Changes / Amendment Permitted.";

        $defaultPayment = "1.  20 % ADVANCE BEFORE DUE DATE\n" .
                         "    BALANCE PAYMENT FROM E.HAJJ";

        return view('hotel_sales.create', compact(
            'hotels',
            'companies',
            'suppliers',
            'clients',
            'roomTypes',
            'nextInvoiceNumber',
            'defaultTerms',
            'defaultCancellation',
            'defaultPayment'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_for'      => 'required|in:company,supplier,client',
            'customer_name' => 'required|string|max:255',
            'invoice_date'  => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.hotel_name' => 'required|string',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $invoiceNumber = $request->invoice_number ?: HotelSale::generateInvoiceNumber();

            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                if (empty($item['hotel_name'])) {
                    continue;
                }

                $qty = max(1, intval($item['quantity'] ?? 1));
                $nights = max(1, intval($item['no_of_nights'] ?? 1));
                $rate = floatval($item['rate_per_unit'] ?? 0);
                $amount = floatval($item['amount'] ?? ($qty * $nights * $rate));

                $totalAmount += $amount;

                $itemsData[] = [
                    'hotel_id'        => !empty($item['hotel_id']) ? $item['hotel_id'] : null,
                    'hotel_name'      => trim($item['hotel_name']),
                    'allotment_basis' => $item['allotment_basis'] ?? 'room',
                    'room_type'       => strtoupper(trim($item['room_type'] ?? 'DOUBLE')),
                    'quantity'        => $qty,
                    'male_beds'       => intval($item['male_beds'] ?? 0),
                    'female_beds'     => intval($item['female_beds'] ?? 0),
                    'meal_plan'       => strtoupper(trim($item['meal_plan'] ?? 'HALF BOARD')),
                    'check_in'        => !empty($item['check_in']) ? date('Y-m-d', strtotime($item['check_in'])) : null,
                    'check_out'       => !empty($item['check_out']) ? date('Y-m-d', strtotime($item['check_out'])) : null,
                    'hijri_check_in'  => !empty($item['hijri_check_in']) ? trim($item['hijri_check_in']) : null,
                    'hijri_check_out' => !empty($item['hijri_check_out']) ? trim($item['hijri_check_out']) : null,
                    'no_of_nights'    => $nights,
                    'rate_per_unit'   => $rate,
                    'amount'          => $amount,
                ];
            }

            if (empty($itemsData)) {
                return back()->withInput()->withErrors(['error' => 'Please add at least one valid hotel reservation item.']);
            }

            $tillReceived = floatval($request->till_received ?? 0);
            $balanceRemaining = max(0, $totalAmount - $tillReceived);

            $supplierId = null;
            $companyId = null;
            if (in_array($request->sale_for, ['company', 'supplier'])) {
                $supplierId = $request->supplier_id ?: $request->company_id;
                $companyId = $request->company_id ?: null;
            }

            $sale = HotelSale::create([
                'invoice_number'      => $invoiceNumber,
                'reservation_title'   => $request->reservation_title ?: 'HAJJ RESERVATION 1448/2027',
                'sale_for'            => $request->sale_for,
                'supplier_id'         => $supplierId,
                'company_id'          => $companyId,
                'client_id'           => $request->sale_for === 'client' ? $request->client_id : null,
                'customer_name'       => $request->customer_name,
                'attn'                => $request->attn ?: null,
                'contact'             => $request->contact ?: null,
                'invoice_date'        => date('Y-m-d', strtotime($request->invoice_date)),
                'currency'            => $request->currency ?: 'SAR',
                'total_amount'        => $totalAmount,
                'till_received'       => $tillReceived,
                'balance_remaining'   => $balanceRemaining,
                'terms_conditions'    => $request->terms_conditions ?: null,
                'cancellation_policy' => $request->cancellation_policy ?: null,
                'payment_procedure'   => $request->payment_procedure ?: null,
                'ceo_name'            => $request->ceo_name ?: 'MUHAMMAD YOUSUF PIRWANI',
                'ceo_title'           => $request->ceo_title ?: 'C.E.O,',
                'status'              => $request->status ?: 'confirmed',
                'notes'               => $request->notes ?: null,
                'created_by'          => auth()->id(),
            ]);

            foreach ($itemsData as $itemData) {
                $sale->items()->create($itemData);
            }

            DB::commit();

            return redirect()
                ->route('hotel-sales.show', $sale->id)
                ->with('success', "Hotel Reservation Invoice {$sale->invoice_number} generated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create hotel sale invoice: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $sale = HotelSale::with(['items.hotel', 'company', 'client'])->findOrFail($id);
        return view('hotel_sales.show', compact('sale'));
    }

    public function edit($id)
    {
        $sale = HotelSale::with(['items', 'supplier', 'company', 'client'])->findOrFail($id);
        $hotels = Hotel::where('status', 'active')->orWhereNull('status')->orderBy('name')->get();
        $companies = Company::orderBy('company_name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $roomTypes = RoomType::where('status', 'active')->orderBy('capacity')->get();

        return view('hotel_sales.edit', compact('sale', 'hotels', 'companies', 'suppliers', 'clients', 'roomTypes'));
    }

    public function update(Request $request, $id)
    {
        $sale = HotelSale::findOrFail($id);

        $request->validate([
            'sale_for'      => 'required|in:company,supplier,client',
            'customer_name' => 'required|string|max:255',
            'invoice_date'  => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.hotel_name' => 'required|string',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                if (empty($item['hotel_name'])) {
                    continue;
                }

                $qty = max(1, intval($item['quantity'] ?? 1));
                $nights = max(1, intval($item['no_of_nights'] ?? 1));
                $rate = floatval($item['rate_per_unit'] ?? 0);
                $amount = floatval($item['amount'] ?? ($qty * $nights * $rate));

                $totalAmount += $amount;

                $itemsData[] = [
                    'hotel_id'        => !empty($item['hotel_id']) ? $item['hotel_id'] : null,
                    'hotel_name'      => trim($item['hotel_name']),
                    'allotment_basis' => $item['allotment_basis'] ?? 'room',
                    'room_type'       => strtoupper(trim($item['room_type'] ?? 'DOUBLE')),
                    'quantity'        => $qty,
                    'male_beds'       => intval($item['male_beds'] ?? 0),
                    'female_beds'     => intval($item['female_beds'] ?? 0),
                    'meal_plan'       => strtoupper(trim($item['meal_plan'] ?? 'HALF BOARD')),
                    'check_in'        => !empty($item['check_in']) ? date('Y-m-d', strtotime($item['check_in'])) : null,
                    'check_out'       => !empty($item['check_out']) ? date('Y-m-d', strtotime($item['check_out'])) : null,
                    'hijri_check_in'  => !empty($item['hijri_check_in']) ? trim($item['hijri_check_in']) : null,
                    'hijri_check_out' => !empty($item['hijri_check_out']) ? trim($item['hijri_check_out']) : null,
                    'no_of_nights'    => $nights,
                    'rate_per_unit'   => $rate,
                    'amount'          => $amount,
                ];
            }

            $tillReceived = floatval($request->till_received ?? 0);
            $balanceRemaining = max(0, $totalAmount - $tillReceived);

            $supplierId = null;
            $companyId = null;
            if (in_array($request->sale_for, ['company', 'supplier'])) {
                $supplierId = $request->supplier_id ?: $request->company_id;
                $companyId = $request->company_id ?: null;
            }

            $sale->update([
                'reservation_title'   => $request->reservation_title ?: $sale->reservation_title,
                'sale_for'            => $request->sale_for,
                'supplier_id'         => $supplierId,
                'company_id'          => $companyId,
                'client_id'           => $request->sale_for === 'client' ? $request->client_id : null,
                'customer_name'       => $request->customer_name,
                'attn'                => $request->attn ?: null,
                'contact'             => $request->contact ?: null,
                'invoice_date'        => date('Y-m-d', strtotime($request->invoice_date)),
                'currency'            => $request->currency ?: 'SAR',
                'total_amount'        => $totalAmount,
                'till_received'       => $tillReceived,
                'balance_remaining'   => $balanceRemaining,
                'terms_conditions'    => $request->terms_conditions ?: null,
                'cancellation_policy' => $request->cancellation_policy ?: null,
                'payment_procedure'   => $request->payment_procedure ?: null,
                'ceo_name'            => $request->ceo_name ?: 'MUHAMMAD YOUSUF PIRWANI',
                'ceo_title'           => $request->ceo_title ?: 'C.E.O,',
                'status'              => $request->status ?: 'confirmed',
                'notes'               => $request->notes ?: null,
            ]);

            // Replace items
            $sale->items()->delete();
            foreach ($itemsData as $itemData) {
                $sale->items()->create($itemData);
            }

            DB::commit();

            return redirect()
                ->route('hotel-sales.show', $sale->id)
                ->with('success', "Hotel Reservation Invoice {$sale->invoice_number} updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update hotel sale invoice: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $sale = HotelSale::findOrFail($id);
        $sale->items()->delete();
        $sale->delete();

        return redirect()
            ->route('hotel-sales.index')
            ->with('success', 'Hotel sale reservation invoice deleted successfully.');
    }
}
