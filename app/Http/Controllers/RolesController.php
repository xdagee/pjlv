<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $roles = Role::all();
            return response()->json(['data' => $roles]);
        }

        // Use admin view for /admin/roles, staff view for /roles
        $view = request()->is('admin/*') ? 'admin.roles.index' : 'staff.roles.index';
        return view($view);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name',
            'role_description' => 'nullable|string|max:255',
        ]);

        $role = Role::create([
            'role_name' => $validated['role_name'],
            'role_description' => $validated['role_description'] ?? null,
            'role_status' => 0, // Default to Inactive (Rules: Active only if assigned)
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
                'role' => $role
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Prevent editing Admin role (ID 1)
        if ($role->id == 1) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit the Super Admin role.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Cannot edit the Super Admin role.');
        }

        $validated = $request->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name,' . $id,
            'role_description' => 'nullable|string|max:255',
        ]);

        // Prevent editing default Admin role name if desired, or handle logic here
        // For now, allow editing but keep ID=1 as Admin conceptually

        $role->update([
            'role_name' => $validated['role_name'],
            'role_description' => $validated['role_description'] ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully!',
                'role' => $role
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deleting Admin role (ID 1)
        if ($role->id == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the Super Admin role.'
            ], 403);
        }

        // Check if role is assigned to any staff
        if ($role->staff()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role because it is assigned to staff members.'
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully!'
        ]);
    }
}
