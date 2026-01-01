<?php

namespace App\Http\Controllers;

use App\Models\LeaveLevel;
use Illuminate\Http\Request;

class LeaveLevelsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of leave levels.
     */
    public function index()
    {
        $leavelevels = LeaveLevel::get();
        return view('admin.leavelevels.index', compact('leavelevels'));
    }

    /**
     * Show the form for creating a new leave level.
     */
    public function create()
    {
        return view('admin.leavelevels.create');
    }

    /**
     * Store a newly created leave level.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'level_name' => 'required|string|max:100|unique:leave_levels',
            'annual_leave_days' => 'required|integer|min:0',
        ]);

        LeaveLevel::create($validated);

        return redirect()->route('leavelevels.index')->with('success', 'Leave level created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $leavelevel = LeaveLevel::findOrFail($id);
        return view('admin.leavelevels.show', compact('leavelevel'));
    }

    /**
     * Show the form for editing.
     */
    public function edit($id)
    {
        $leavelevel = LeaveLevel::findOrFail($id);
        return view('admin.leavelevels.edit', compact('leavelevel'));
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $leavelevel = LeaveLevel::findOrFail($id);

        $validated = $request->validate([
            'level_name' => 'required|string|max:100|unique:leave_levels,level_name,' . $id,
            'annual_leave_days' => 'required|integer|min:0',
        ]);

        $leavelevel->update($validated);

        return redirect()->route('leavelevels.index')->with('success', 'Leave level updated successfully!');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        $leavelevel = LeaveLevel::findOrFail($id);

        // Check usage if Staff uses this level?
        if ($leavelevel->staff()->exists()) {
            return redirect()->route('leavelevels.index')->with('error', 'Cannot delete leave level assigned to staff.');
        }

        $leavelevel->delete();

        return redirect()->route('leavelevels.index')->with('success', 'Leave level deleted successfully!');
    }
}
