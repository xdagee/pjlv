<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\ApproveLeaveRequest;
use App\StaffLeave;
use App\LeaveType;
use App\LeaveAction;
use App\LeaveStatus;
use App\Mail\LeaveRequestSubmitted;
use App\Mail\LeaveRequestApproved;
use App\Mail\LeaveRequestRejected;
use App\Services\LeaveBalanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StaffLeavesController extends Controller
{
    protected $leaveService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->leaveService = new LeaveBalanceService();
    }

    /**
     * Display a listing of leave requests.
     */
    public function index()
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff) {
            return redirect('/dashboard')->with('error', 'Staff profile not found.');
        }

        // HR and Admin see all leaves, others see only their own
        if (in_array($staff->role_id, [1, 2])) {
            $leaves = StaffLeave::with(['staff', 'leaveType', 'leaveAction.leaveStatus'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $leaves = StaffLeave::where('staff_id', $staff->id)
                ->with(['leaveType', 'leaveAction.leaveStatus'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('leaves.index', compact('leaves', 'staff'));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $leaveTypes = LeaveType::all();
        $staff = Auth::user()->staff;
        $balance = $staff ? $this->leaveService->getBalanceBreakdown($staff->id) : null;

        return view('leaves.apply', compact('leaveTypes', 'balance'));
    }

    /**
     * Store a newly created leave request.
     */
    public function store(StoreLeaveRequest $request)
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            return redirect()->back()->with('error', 'Staff profile not found.');
        }

        // Create leave request
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'leave_days' => $request->leave_days,
        ]);

        // Create initial leave action (Pending)
        $pendingStatus = LeaveStatus::where('status_name', 'Unattended')->first();
        LeaveAction::create([
            'leave_id' => $leave->id,
            'status_id' => $pendingStatus->id ?? 1,
            'actionby' => $staff->id,
        ]);

        // Send notification to supervisor/HR
        $this->notifySupervisor($leave, $staff);

        return redirect('/leaves')->with('success', 'Leave request submitted successfully!');
    }

    /**
     * Display the specified leave request.
     */
    public function show($id)
    {
        $leave = StaffLeave::with(['staff', 'leaveType', 'leaveAction.leaveStatus', 'leaveAction.actionBy'])
            ->findOrFail($id);

        $user = Auth::user();
        $staff = $user->staff;

        // Check authorization
        if (!$staff || ($leave->staff_id !== $staff->id && !in_array($staff->role_id, [1, 2, 3, 4]))) {
            return redirect('/leaves')->with('error', 'You are not authorized to view this request.');
        }

        return view('leaves.show', compact('leave', 'staff'));
    }

    /**
     * Show edit form for a leave request.
     */
    public function edit($id)
    {
        $leave = StaffLeave::findOrFail($id);
        $user = Auth::user();
        $staff = $user->staff;

        // Only allow editing own pending requests
        if (!$staff || $leave->staff_id !== $staff->id) {
            return redirect('/leaves')->with('error', 'You can only edit your own requests.');
        }

        $leaveTypes = LeaveType::all();
        return view('leaves.edit', compact('leave', 'leaveTypes'));
    }

    /**
     * Update the specified leave request (for approvals).
     */
    public function update(ApproveLeaveRequest $request, $id)
    {
        $leave = StaffLeave::findOrFail($id);
        $user = Auth::user();
        $staff = $user->staff;

        $action = $request->action;
        $reason = $request->reason;

        // Determine the status based on action
        $statusMap = [
            'approve' => 'Approved',
            'reject' => 'Rejected',
            'recommend' => 'Recommended',
        ];

        $status = LeaveStatus::where('status_name', $statusMap[$action])->first();

        // Create leave action record
        LeaveAction::create([
            'leave_id' => $leave->id,
            'status_id' => $status->id,
            'actionby' => $staff->id,
            'action_reason' => $reason,
        ]);

        // Send notification to applicant
        $applicant = $leave->staff;
        if ($applicant && $applicant->user && $applicant->user->email) {
            try {
                if ($action === 'approve') {
                    Mail::to($applicant->user->email)->send(new LeaveRequestApproved($leave, $applicant, $staff));
                } elseif ($action === 'reject') {
                    Mail::to($applicant->user->email)->send(new LeaveRequestRejected($leave, $applicant, $staff, $reason));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send notification: ' . $e->getMessage());
            }
        }

        return redirect('/leaves/' . $id)->with('success', 'Leave request has been ' . $statusMap[$action] . '.');
    }

    /**
     * Cancel a leave request.
     */
    public function cancel($id)
    {
        $leave = StaffLeave::findOrFail($id);
        $user = Auth::user();
        $staff = $user->staff;

        // Only allow canceling own requests
        if (!$staff || $leave->staff_id !== $staff->id) {
            return redirect('/leaves')->with('error', 'You can only cancel your own requests.');
        }

        $cancelledStatus = LeaveStatus::where('status_name', 'Cancelled')->first();

        LeaveAction::create([
            'leave_id' => $leave->id,
            'status_id' => $cancelledStatus->id ?? 6,
            'actionby' => $staff->id,
        ]);

        return redirect('/leaves')->with('success', 'Leave request cancelled successfully.');
    }

    /**
     * Remove the specified leave request.
     */
    public function destroy($id)
    {
        $leave = StaffLeave::findOrFail($id);
        $user = Auth::user();
        $staff = $user->staff;

        // Only HR/Admin can delete, or owner can delete pending requests
        if (!$staff || (!in_array($staff->role_id, [1, 2]) && $leave->staff_id !== $staff->id)) {
            return redirect('/leaves')->with('error', 'You are not authorized to delete this request.');
        }

        $leave->leaveAction()->delete();
        $leave->delete();

        return redirect('/leaves')->with('success', 'Leave request deleted successfully.');
    }

    /**
     * Notify supervisor about new leave request.
     */
    private function notifySupervisor(StaffLeave $leave, $applicant)
    {
        // Find HR users to notify
        $hrStaff = \App\Staff::where('role_id', 2)->where('is_active', 1)->first();

        if ($hrStaff && $hrStaff->user && $hrStaff->user->email) {
            try {
                Mail::to($hrStaff->user->email)->send(new LeaveRequestSubmitted($leave, $applicant));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send leave notification: ' . $e->getMessage());
            }
        }
    }
}
