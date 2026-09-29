<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VehicleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('vehicle_view');

        $vehicles = Vehicle::latest()->get();
        $trashCount = Vehicle::onlyTrashed()->count();

        return view('vehicle.index', compact('vehicles', 'trashCount'));
    }

    public function create()
    {
        $this->authorize('vehicle_create');

        return view('vehicle.create');
    }

    public function store(Request $request)
    {
        $this->authorize('vehicle_create');

        $request->validate([
            'vehicle_type'  => 'nullable|string|max:150',
            'brand_name'    => 'nullable|string|max:150',
            'model_year'    => 'nullable|string|max:50',
            'plate_number'  => 'nullable|string|max:50',
            'supplier_name' => 'nullable|string|max:150',
            'status'        => 'required|in:active,inactive',
        ]);

        try {
            $vehicle = Vehicle::create($request->only([
                'vehicle_type',
                'brand_name',
                'model_year',
                'plate_number',
                'supplier_name',
                'status',
            ]));

            logUserActivity('Vehicle Created', 'Type: ' . $vehicle->vehicle_type . ' | Plate: ' . $vehicle->plate_number, $vehicle->id, 'Vehicle');

            return redirect()->route('vehicle.index')->with('success', 'Vehicle created successfully.');
        } catch (Exception $e) {
            Log::error('Vehicle creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating vehicle: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('vehicle_edit');

        $vehicle = Vehicle::findOrFail($id);

        return view('vehicle.edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('vehicle_edit');

        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'vehicle_type'  => 'nullable|string|max:150',
            'brand_name'    => 'nullable|string|max:150',
            'model_year'    => 'nullable|string|max:50',
            'plate_number'  => 'nullable|string|max:50',
            'supplier_name' => 'nullable|string|max:150',
            'status'        => 'required|in:active,inactive',
        ]);

        try {
            $vehicle->update($request->only([
                'vehicle_type',
                'brand_name',
                'model_year',
                'plate_number',
                'supplier_name',
                'status',
            ]));

            logUserActivity('Vehicle Updated', 'Type: ' . $vehicle->vehicle_type . ' | Plate: ' . $vehicle->plate_number, $vehicle->id, 'Vehicle');

            return redirect()->route('vehicle.index')->with('success', 'Vehicle updated successfully.');
        } catch (Exception $e) {
            Log::error('Vehicle update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating vehicle: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('vehicle_trash');

        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();

            logUserActivity('Vehicle Deleted', 'Type: ' . $vehicle->vehicle_type . ' | Plate: ' . $vehicle->plate_number, $vehicle->id, 'Vehicle');

            return redirect()->route('vehicle.index')->with('success', 'Vehicle moved to trash successfully.');
        } catch (Exception $e) {
            Log::error('Vehicle deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting vehicle.');
        }
    }

    public function trash()
    {
        $this->authorize('vehicle_trash_view');

        $vehicles = Vehicle::onlyTrashed()->latest()->get();

        return view('vehicle.trash', compact('vehicles'));
    }

    public function restore($id)
    {
        $this->authorize('vehicle_restore');

        try {
            $vehicle = Vehicle::onlyTrashed()->findOrFail($id);
            $vehicle->restore();

            logUserActivity('Vehicle Restored', 'Type: ' . $vehicle->vehicle_type . ' | Plate: ' . $vehicle->plate_number, $vehicle->id, 'Vehicle');

            return redirect()->route('vehicle.trash')->with('success', 'Vehicle restored successfully.');
        } catch (Exception $e) {
            Log::error('Vehicle restore failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error restoring vehicle.');
        }
    }
}
