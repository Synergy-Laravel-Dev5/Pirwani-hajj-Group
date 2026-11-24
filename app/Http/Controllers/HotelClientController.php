<?php

namespace App\Http\Controllers;

use App\Models\HotelClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HotelClientController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = HotelClient::query()->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%")
                  ->orWhere('contact', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $clients = $query->paginate(20)->withQueryString();

        $totalCount = HotelClient::count();
        $activeCount = HotelClient::where('status', 'active')->count();
        $inactiveCount = HotelClient::where('status', 'inactive')->count();

        return view('hotel_clients.index', compact(
            'clients',
            'totalCount',
            'activeCount',
            'inactiveCount'
        ));
    }

    public function create()
    {
        return view('hotel_clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact'      => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
            'status'       => 'nullable|string|in:active,inactive',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['created_by'] = Auth::id();

        $client = HotelClient::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel client created successfully.',
                'client'  => $client,
            ]);
        }

        return redirect()->route('hotel-clients.index')->with('success', 'Hotel client created successfully.');
    }

    public function edit($id)
    {
        $client = HotelClient::findOrFail($id);
        return view('hotel_clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $client = HotelClient::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact'      => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
            'status'       => 'nullable|string|in:active,inactive',
        ]);

        $client->update($validated);

        return redirect()->route('hotel-clients.index')->with('success', 'Hotel client updated successfully.');
    }

    public function destroy($id)
    {
        $client = HotelClient::findOrFail($id);
        $client->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel client deleted successfully.',
            ]);
        }

        return redirect()->route('hotel-clients.index')->with('success', 'Hotel client deleted successfully.');
    }

    public function apiList(Request $request)
    {
        $search = trim($request->get('search', ''));
        $query = HotelClient::where('status', 'active');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('name')->take(30)->get();

        return response()->json($clients);
    }
}
