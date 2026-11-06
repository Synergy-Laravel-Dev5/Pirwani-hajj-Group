<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('role_view');
        $roles = Role::latest()->get();
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('role_create');
        $permissions = Permission::all();
        return view('role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('role_create');
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name'       => $request->name,
                'guard_name' => 'web'
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            DB::commit();

            logUserActivity('Role Created', 'Role: ' . $role->name . ' | Permissions: ' . count($request->permissions ?? []), $role->id, 'Role');

            return redirect()->route('role.index')->with('success', 'Role Created Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('role_edit');
        $role            = Role::findOrFail($id);
        $permissions     = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('role_edit');
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id
        ]);

        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);

            $role->update([
                'name'       => $request->name,
                'guard_name' => 'web'
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            DB::commit();

            logUserActivity('Role Updated', 'Role: ' . $role->name . ' | Permissions: ' . count($request->permissions ?? []), $role->id, 'Role');

            return redirect()->route('role.index')->with('success', 'Role Updated Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('role_trash');
        $role = Role::findOrFail($id);

        logUserActivity('Role Deleted', 'Role: ' . $role->name, $role->id, 'Role');

        $role->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', 'Role Deleted');
    }
}
