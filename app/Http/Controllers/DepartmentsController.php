<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
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
            $departments = Department::withCount('staff')->get();
            return response()->json(['data' => $departments]);
        }
        return view('admin.departments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $department = Department::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Department created successfully!',
                'department' => $department
            ]);
        }

        return redirect()->route('departments.index')->with('success', 'Department created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $department = Department::withCount('staff')->findOrFail($id);
        return response()->json($department);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $department = Department::findOrFail($id);
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        $department->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Department updated successfully!',
                'department' => $department
            ]);
        }

        return redirect()->route('departments.index')->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);

        // Check if assigned to any staff
        if ($department->staff()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete department because it has staff members assigned.'
            ], 400);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully!'
        ]);
    }
}
