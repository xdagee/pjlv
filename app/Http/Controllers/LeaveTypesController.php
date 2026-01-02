<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of leave types.
     */
    public function index()
    {
        $leavetypes = LeaveType::all();

        // Use admin view for /admin/leavetypes, staff view for /leavetypes
        $view = request()->is('admin/*') ? 'admin.leavetypes.index' : 'staff.leavetypes.index';
        return view($view, compact('leavetypes'));
    }

    /**
     * Show the form for creating a new leave type.
     */
    public function create()
    {
        return view('admin.leavetypes.create');
    }

    /**
     * Store a newly created leave type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_name' => 'required|string|max:100|unique:leave_types',
            'leave_duration' => 'required|integer|min:0',
        ]);

        LeaveType::create($validated);

        return redirect('/leavetypes')->with('success', 'Leave type created successfully.');
    }

    /**
     * Display the specified leave type.
     */
    public function show($id)
    {
        $leavetype = LeaveType::findOrFail($id);
        return view('admin.leavetypes.show', compact('leavetype'));
    }

    /**
     * Show the form for editing the specified leave type.
     */
    public function edit($id)
    {
        $leavetype = LeaveType::findOrFail($id);
        return view('admin.leavetypes.edit', compact('leavetype'));
    }

    /**
     * Update the specified leave type.
     */
    public function update(Request $request, $id)
    {
        $leavetype = LeaveType::findOrFail($id);

        $validated = $request->validate([
            'leave_type_name' => 'required|string|max:100|unique:leave_types,leave_type_name,' . $id,
            'leave_duration' => 'required|integer|min:0',
        ]);

        $leavetype->update($validated);

        return redirect('/leavetypes')->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified leave type.
     */
    public function destroy($id)
    {
        $leavetype = LeaveType::findOrFail($id);

        // Check if leave type is in use
        if ($leavetype->staffLeaves()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete leave type that is in use.');
        }

        $leavetype->delete();

        return redirect()->route('leavetypes.index')->with('success', 'Leave type deleted successfully.');
    }
}
