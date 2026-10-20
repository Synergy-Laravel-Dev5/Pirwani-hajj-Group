<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query()->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('supplier_code', 'like', "%{$s}%")
                  ->orWhere('contact_person', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type') && $request->service_type !== 'all') {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('city') && $request->city !== 'all') {
            $query->where('city', $request->city);
        }

        $suppliers = $query->paginate(20)->withQueryString();

        $totalCount = Supplier::count();
        $activeCount = Supplier::where('status', 'active')->count();
        $inactiveCount = Supplier::where('status', 'inactive')->count();
        $trashCount = Supplier::onlyTrashed()->count();

        $serviceTypes = Supplier::whereNotNull('service_type')->distinct()->pluck('service_type');
        $cities = Supplier::whereNotNull('city')->distinct()->pluck('city');

        return view('suppliers.index', compact(
            'suppliers',
            'totalCount',
            'activeCount',
            'inactiveCount',
            'trashCount',
            'serviceTypes',
            'cities'
        ));
    }

    public function create()
    {
        $nextCode = Supplier::generateSupplierCode();
        return view('suppliers.create', compact('nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'supplier_code'       => 'nullable|string|max:50|unique:suppliers,supplier_code',
            'contact_person'      => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:50',
            'mobile'              => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:100',
            'service_type'        => 'nullable|string|max:100',
            'country'             => 'nullable|string|max:100',
            'city'                => 'nullable|string|max:100',
            'address'             => 'nullable|string',
            'tax_number'          => 'nullable|string|max:100',
            'commercial_reg_no'   => 'nullable|string|max:100',
            'currency'            => 'nullable|string|max:10',
            'opening_balance'     => 'nullable|numeric',
            'bank_name'           => 'nullable|string|max:255',
            'bank_account_title'  => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_iban'           => 'nullable|string|max:100',
            'status'              => 'required|in:active,inactive',
            'notes'               => 'nullable|string',
        ]);

        try {
            $data = $request->all();
            if (empty($data['supplier_code'])) {
                $data['supplier_code'] = Supplier::generateSupplierCode();
            }
            $data['created_by'] = Auth::id();

            $supplier = Supplier::create($data);

            if (function_exists('logUserActivity')) {
                logUserActivity('Supplier Created', "Supplier '{$supplier->name}' (Code: {$supplier->supplier_code}) created.", $supplier->id, 'Supplier');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Supplier created successfully!',
                    'supplier' => $supplier,
                ]);
            }

            return redirect()->route('supplier.index')->with('success', 'Supplier created successfully.');
        } catch (Exception $e) {
            Log::error('Supplier creation failed: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Error creating supplier: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $supplier = Supplier::with(['inventories.hotel', 'hotelSales'])->findOrFail($id);

        $totalAllotmentLines = $supplier->inventories->count();
        $totalStockRooms = $supplier->inventories->sum('total_rooms');
        $totalStockBeds = $supplier->inventories->sum('total_beds');
        $totalSalesCount = $supplier->hotelSales->count();
        $totalSalesAmount = $supplier->hotelSales->sum('total_amount');

        return view('suppliers.show', compact(
            'supplier',
            'totalAllotmentLines',
            'totalStockRooms',
            'totalStockBeds',
            'totalSalesCount',
            'totalSalesAmount'
        ));
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name'                => 'required|string|max:255',
            'supplier_code'       => ['nullable', 'string', 'max:50', Rule::unique('suppliers', 'supplier_code')->ignore($supplier->id)],
            'contact_person'      => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:50',
            'mobile'              => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:100',
            'service_type'        => 'nullable|string|max:100',
            'country'             => 'nullable|string|max:100',
            'city'                => 'nullable|string|max:100',
            'address'             => 'nullable|string',
            'tax_number'          => 'nullable|string|max:100',
            'commercial_reg_no'   => 'nullable|string|max:100',
            'currency'            => 'nullable|string|max:10',
            'opening_balance'     => 'nullable|numeric',
            'bank_name'           => 'nullable|string|max:255',
            'bank_account_title'  => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_iban'           => 'nullable|string|max:100',
            'status'              => 'required|in:active,inactive',
            'notes'               => 'nullable|string',
        ]);

        try {
            $data = $request->all();
            $supplier->update($data);

            if (function_exists('logUserActivity')) {
                logUserActivity('Supplier Updated', "Supplier '{$supplier->name}' updated.", $supplier->id, 'Supplier');
            }

            return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
        } catch (Exception $e) {
            Log::error('Supplier update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating supplier: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $name = $supplier->name;
            $supplier->delete();

            if (function_exists('logUserActivity')) {
                logUserActivity('Supplier Deleted', "Supplier '{$name}' moved to trash.", $id, 'Supplier');
            }

            return redirect()->route('supplier.index')->with('success', "Supplier '{$name}' moved to trash successfully.");
        } catch (Exception $e) {
            return back()->with('error', 'Error deleting supplier: ' . $e->getMessage());
        }
    }

    public function trash()
    {
        $trashedSuppliers = Supplier::onlyTrashed()->latest()->paginate(20);
        $trashCount = Supplier::onlyTrashed()->count();

        return view('suppliers.trash', compact('trashedSuppliers', 'trashCount'));
    }

    public function restore($id)
    {
        try {
            $supplier = Supplier::onlyTrashed()->findOrFail($id);
            $supplier->restore();

            if (function_exists('logUserActivity')) {
                logUserActivity('Supplier Restored', "Supplier '{$supplier->name}' restored.", $id, 'Supplier');
            }

            return redirect()->route('supplier.trash')->with('success', "Supplier '{$supplier->name}' restored successfully.");
        } catch (Exception $e) {
            return back()->with('error', 'Error restoring supplier: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $supplier = Supplier::onlyTrashed()->findOrFail($id);
            $name = $supplier->name;
            $supplier->forceDelete();

            if (function_exists('logUserActivity')) {
                logUserActivity('Supplier Permanently Deleted', "Supplier '{$name}' permanently deleted.", $id, 'Supplier');
            }

            return redirect()->route('supplier.trash')->with('success', "Supplier '{$name}' permanently deleted.");
        } catch (Exception $e) {
            return back()->with('error', 'Error permanently deleting supplier: ' . $e->getMessage());
        }
    }
}
