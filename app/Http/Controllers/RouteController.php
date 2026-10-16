<?php

namespace App\Http\Controllers;

use App\Models\Route as RouteModel;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RouteController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        if (!isCompanyUser()) {
            $this->authorize('route_view');
        }

        $routes = RouteModel::latest()->get();
        $trashCount = RouteModel::onlyTrashed()->count();

        return view('route.index', compact('routes', 'trashCount'));
    }

    public function create()
    {
        if (!isCompanyUser()) {
            $this->authorize('route_create');
        }

        return view('route.create');
    }

    public function store(Request $request)
    {
        if (!isCompanyUser()) {
            $this->authorize('route_create');
        }

        $request->validate([
            'start_place' => 'required|string|max:255',
            'end_place'   => 'required|string|max:255',
            'status'      => 'nullable|in:active,inactive',
        ]);

        $route = RouteModel::create([
            'start_place' => $request->start_place,
            'end_place'   => $request->end_place,
            'status'      => $request->status ?? 'active',
        ]);

        $companyId = getAuthCompanyId();
        $compName = $companyId ? (getAuthCompany()->company_name ?? (getAuthCompany()->name ?? 'Company')) : 'Admin';

        logUserActivity('Route Created', "Route: {$route->start_place} to {$route->end_place} | Added by: {$compName}", $route->id, 'Route', $companyId);

        if ($companyId) {
            sendAppNotification(
                'New Route Added by Company',
                "Company '{$compName}' added Route '{$route->start_place} to {$route->end_place}'.",
                route('route.index'),
                'info',
                'mdi-map-marker-path',
                true // for_admin
            );
        }

        return redirect()->route('route.index')->with('success', 'Route added successfully.');
    }

    public function edit($id)
    {
        if (!isCompanyUser()) {
            $this->authorize('route_edit');
        }

        $route = RouteModel::findOrFail($id);
        return view('route.edit', compact('route'));
    }

    public function update(Request $request, $id)
    {
        if (!isCompanyUser()) {
            $this->authorize('route_edit');
        }

        $route = RouteModel::findOrFail($id);

        $request->validate([
            'start_place' => 'required|string|max:255',
            'end_place'   => 'required|string|max:255',
            'status'      => 'nullable|in:active,inactive',
        ]);

        $route->update([
            'start_place' => $request->start_place,
            'end_place'   => $request->end_place,
            'status'      => $request->status ?? 'active',
        ]);

        logUserActivity('Route Updated', 'Start: ' . $route->start_place . ' | End: ' . $route->end_place, $route->id, 'Route');

        return redirect()->route('route.index')->with('success', 'Route updated successfully.');
    }

    public function destroy($id)
    {
        if (!isCompanyUser()) {
            $this->authorize('route_trash');
        }

        $route = RouteModel::findOrFail($id);

        logUserActivity('Route Deleted', 'Start: ' . $route->start_place . ' | End: ' . $route->end_place, $route->id, 'Route');

        $route->delete();

        return redirect()->route('route.index')->with('success', 'Route moved to trash successfully.');
    }

    public function trash()
    {
        if (!isCompanyUser()) {
            $this->authorize('route_trash_view');
        }

        $routes = RouteModel::onlyTrashed()->latest()->get();
        return view('route.trash', compact('routes'));
    }

    public function restore($id)
    {
        if (!isCompanyUser()) {
            $this->authorize('route_restore');
        }

        $route = RouteModel::onlyTrashed()->findOrFail($id);
        $route->restore();

        logUserActivity('Route Restored', 'Start: ' . $route->start_place . ' | End: ' . $route->end_place, $route->id, 'Route');

        return redirect()->route('route.index')->with('success', 'Route restored successfully.');
    }
}
