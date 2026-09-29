<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AirlineController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('airline_view');

        $airlines = Airline::latest()->get();
        $trashCount = Airline::onlyTrashed()->count();

        return view('airline.index', compact('airlines', 'trashCount'));
    }

    public function create()
    {
        $this->authorize('airline_create');

        return view('airline.create');
    }

    public function store(Request $request)
    {
        $this->authorize('airline_create');

        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'nullable|string|max:100',
            'iata_code' => 'nullable|string|max:50',
            'icao_code' => 'nullable|string|max:50',
            'country'   => 'nullable|string|max:150',
            'call_sign' => 'nullable|string|max:150',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ffnumber'  => 'nullable|string|max:100',
            'status'    => 'required|in:active,inactive',
        ]);

        try {
            $data = $request->except('logo');

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/airlines'), $filename);
                $data['logo'] = 'uploads/airlines/' . $filename;
            }

            $airline = Airline::create($data);

            logUserActivity('Airline Created', 'Name: ' . $airline->name . ' | Code: ' . $airline->code, $airline->id, 'Airline');

            return redirect()->route('airline.index')->with('success', 'Airline created successfully.');
        } catch (Exception $e) {
            Log::error('Airline creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating airline: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('airline_edit');

        $airline = Airline::findOrFail($id);

        return view('airline.edit', compact('airline'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('airline_edit');

        $airline = Airline::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'nullable|string|max:100',
            'iata_code' => 'nullable|string|max:50',
            'icao_code' => 'nullable|string|max:50',
            'country'   => 'nullable|string|max:150',
            'call_sign' => 'nullable|string|max:150',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ffnumber'  => 'nullable|string|max:100',
            'status'    => 'required|in:active,inactive',
        ]);

        try {
            $data = $request->except('logo');

            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($airline->logo && file_exists(public_path($airline->logo))) {
                    unlink(public_path($airline->logo));
                }

                $file = $request->file('logo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/airlines'), $filename);
                $data['logo'] = 'uploads/airlines/' . $filename;
            }

            $airline->update($data);

            logUserActivity('Airline Updated', 'Name: ' . $airline->name, $airline->id, 'Airline');

            return redirect()->route('airline.index')->with('success', 'Airline updated successfully.');
        } catch (Exception $e) {
            Log::error('Airline update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating airline: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('airline_trash');

        try {
            $airline = Airline::findOrFail($id);
            $airline->delete();

            logUserActivity('Airline Deleted', 'Name: ' . $airline->name, $airline->id, 'Airline');

            return redirect()->route('airline.index')->with('success', 'Airline moved to trash successfully.');
        } catch (Exception $e) {
            Log::error('Airline deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting airline.');
        }
    }

    public function trash()
    {
        $this->authorize('airline_trash_view');

        $airlines = Airline::onlyTrashed()->latest()->get();

        return view('airline.trash', compact('airlines'));
    }

    public function restore($id)
    {
        $this->authorize('airline_restore');

        try {
            $airline = Airline::onlyTrashed()->findOrFail($id);
            $airline->restore();

            logUserActivity('Airline Restored', 'Name: ' . $airline->name, $airline->id, 'Airline');

            return redirect()->route('airline.trash')->with('success', 'Airline restored successfully.');
        } catch (Exception $e) {
            Log::error('Airline restore failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error restoring airline.');
        }
    }
}
