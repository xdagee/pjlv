<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveAction;
use App\StaffLeave;
use App\LeaveStatus;
use Illuminate\Support\Facades\Auth;

class LeaveActionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of leave actions.
     */
    public function index()
    {
        $leaveactions = LeaveAction::with(['staffLeave.staff', 'leaveStatus', 'actionBy'])
            ->orderBy('action_date', 'desc')
            ->paginate(20);

        return view('leaveactions.index', compact('leaveactions'));
    }

    /**
     * Show the form for creating a new leave action.
     */
    public function create()
    {
        $pendingLeaves = StaffLeave::whereDoesntHave('leaveAction', function ($q) {
            $q->whereIn('status_id', [2, 3, 5, 6]); // Approved, Disapproved, Rejected, Cancelled
        })->with('staff', 'leaveType')->get();

        $statuses = LeaveStatus::all();

        return view('leaveactions.create', compact('pendingLeaves', 'statuses'));
    }

    /**
     * Store a newly created leave action.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_id' => 'required|exists:staff_leaves,id',
            'status_id' => 'required|exists:leave_statuses,id',
        ]);

        $staff = Auth::user()->staff;

        LeaveAction::create([
            'leave_id' => $validated['leave_id'],
            'status_id' => $validated['status_id'],
            'actionby' => $staff->id,
        ]);

        return redirect('/leaveactions')->with('success', 'Leave action recorded successfully.');
    }

    /**
     * Display the specified leave action.
     */
    public function show($id)
    {
        $leaveaction = LeaveAction::with(['staffLeave.staff', 'leaveStatus', 'actionBy'])
            ->findOrFail($id);

        return view('leaveactions.show', compact('leaveaction'));
    }

    /**
     * Show the form for editing the specified leave action.
     */
    public function edit($id)
    {
        $leaveaction = LeaveAction::findOrFail($id);
        $statuses = LeaveStatus::all();

        return view('leaveactions.edit', compact('leaveaction', 'statuses'));
    }

    /**
     * Update the specified leave action.
     */
    public function update(Request $request, $id)
    {
        $leaveaction = LeaveAction::findOrFail($id);

        $validated = $request->validate([
            'status_id' => 'required|exists:leave_statuses,id',
        ]);

        $leaveaction->update($validated);

        return redirect('/leaveactions')->with('success', 'Leave action updated successfully.');
    }

    /**
     * Remove the specified leave action.
     */
    public function destroy($id)
    {
        $leaveaction = LeaveAction::findOrFail($id);
        $leaveaction->delete();

        return redirect('/leaveactions')->with('success', 'Leave action deleted successfully.');
    }
}
