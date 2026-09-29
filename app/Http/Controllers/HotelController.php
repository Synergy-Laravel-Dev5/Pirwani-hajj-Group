<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HotelController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('hotel_view');

        $hotels = Hotel::latest()->get();
        $trashCount = Hotel::onlyTrashed()->count();

        return view('hotel.index', compact('hotels', 'trashCount'));
    }

    public function create()
    {
        $this->authorize('hotel_create');

        return view('hotel.create');
    }

    public function store(Request $request)
    {
        $this->authorize('hotel_create');

        $request->validate([
            'name'                   => 'required|string|max:255',
            'hotel_number'           => 'nullable|string|max:100',
            'code'                   => 'nullable|string|max:100',
            'address'                => 'nullable|string',
            'contact'                => 'nullable|string|max:50',
            'email'                  => 'nullable|email|max:100',
            'place'                  => 'nullable|string|max:150',
            'accommodation_type'     => 'nullable|string|max:150',
            'accommodation_category' => 'nullable|string|max:150',
            'logo'                   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'                 => 'required|in:active,inactive',
        ]);

        try {
            $data = $request->except('logo');

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/hotels'), $filename);
                $data['logo'] = 'uploads/hotels/' . $filename;
            }

            $hotel = Hotel::create($data);

            logUserActivity('Hotel Created', 'Name: ' . $hotel->name . ' | Number: ' . $hotel->hotel_number, $hotel->id, 'Hotel');

            return redirect()->route('hotel.index')->with('success', 'Hotel created successfully.');
        } catch (Exception $e) {
            Log::error('Hotel creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating hotel: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('hotel_edit');

        $hotel = Hotel::findOrFail($id);

        return view('hotel.edit', compact('hotel'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('hotel_edit');

        $hotel = Hotel::findOrFail($id);

        $request->validate([
            'name'                   => 'required|string|max:255',
            'hotel_number'           => 'nullable|string|max:100',
            'code'                   => 'nullable|string|max:100',
            'address'                => 'nullable|string',
            'contact'                => 'nullable|string|max:50',
            'email'                  => 'nullable|email|max:100',
            'place'                  => 'nullable|string|max:150',
            'accommodation_type'     => 'nullable|string|max:150',
            'accommodation_category' => 'nullable|string|max:150',
            'logo'                   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'                 => 'required|in:active,inactive',
        ]);

        try {
            $data = $request->except('logo');

            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($hotel->logo && file_exists(public_path($hotel->logo))) {
                    unlink(public_path($hotel->logo));
                }

                $file = $request->file('logo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/hotels'), $filename);
                $data['logo'] = 'uploads/hotels/' . $filename;
            }

            $hotel->update($data);

            logUserActivity('Hotel Updated', 'Name: ' . $hotel->name, $hotel->id, 'Hotel');

            return redirect()->route('hotel.index')->with('success', 'Hotel updated successfully.');
        } catch (Exception $e) {
            Log::error('Hotel update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating hotel: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('hotel_trash');

        try {
            $hotel = Hotel::findOrFail($id);
            $hotel->delete();

            logUserActivity('Hotel Deleted', 'Name: ' . $hotel->name, $hotel->id, 'Hotel');

            return redirect()->route('hotel.index')->with('success', 'Hotel moved to trash successfully.');
        } catch (Exception $e) {
            Log::error('Hotel deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting hotel.');
        }
    }

    public function trash()
    {
        $this->authorize('hotel_trash_view');

        $hotels = Hotel::onlyTrashed()->latest()->get();

        return view('hotel.trash', compact('hotels'));
    }

    public function restore($id)
    {
        $this->authorize('hotel_restore');

        try {
            $hotel = Hotel::onlyTrashed()->findOrFail($id);
            $hotel->restore();

            logUserActivity('Hotel Restored', 'Name: ' . $hotel->name, $hotel->id, 'Hotel');

            return redirect()->route('hotel.trash')->with('success', 'Hotel restored successfully.');
        } catch (Exception $e) {
            Log::error('Hotel restore failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error restoring hotel.');
        }
    }
}
