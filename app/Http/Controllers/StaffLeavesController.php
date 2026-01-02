<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\ApproveLeaveRequest;
use App\Models\StaffLeave;
use App\Models\LeaveType;
use App\Models\LeaveAction;
use App\Models\LeaveStatus;
use App\Models\Staff;
use App\Mail\LeaveRequestSubmitted;
use App\Mail\LeaveRequestApproved;
use App\Mail\LeaveRequestRejected;
use App\Services\LeaveBalanceService;
use App\Services\SettingsService;
use App\Enums\LeaveStatusEnum;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use App\Services\LeaveCalculatorService;

class StaffLeavesController extends Controller
{
    protected $leaveService;
    protected $leaveCalculator;
    protected $settingsService;

    public function __construct(LeaveCalculatorService $leaveCalculator, SettingsService $settingsService)
    {
        $this->middleware('auth');
        $this->leaveService = new LeaveBalanceService();
        $this->leaveCalculator = $leaveCalculator;
        $this->settingsService = $settingsService;
    }

    /**
     * Display a listing of leave requests (personal leaves only - My Leaves).
     */
    public function index()
    {
        $user = Auth::user();
        $staff = $user->staff;
        $perPage = $this->settingsService->get('display.pagination_size', 15);

        if (!$staff) {
            return redirect('/dashboard')->with('error', 'Staff profile not found.');
        }

        // Always show only the user's own leaves (My Leaves)
        // All Leaves for all staff is handled by AdminLeavesController at /all-leaves
        $leaves = StaffLeave::where('staff_id', $staff->id)
            ->with(['leaveType', 'leaveAction.leaveStatus'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

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

        // Calculate working days automatically (Ghana Labour Act Compliance)
        $calculatedDays = $this->leaveCalculator->calculateWorkingDays($request->start_date, $request->end_date);

        if ($calculatedDays <= 0) {
            return redirect()->back()->with('error', 'Invalid leave duration. Ensure start date is before end date and includes working days.');
        }

        // Strict Compliance: Check balance before applying
        if (!$this->leaveService->canApply($staff->id, $calculatedDays)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Insufficient leave balance. You requested $calculatedDays working days, but do not have enough.");
        }

        // Create leave request
        $leave = StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'leave_days' => $calculatedDays, // Use calculated days, ignore request->leave_days
        ]);

        // Create initial leave action (Pending)
        LeaveAction::create([
            'leave_id' => $leave->id,
            'status_id' => LeaveStatusEnum::UNATTENDED->value,
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

        // Check authorization - owner or any approver role can view
        $approverRoleIds = array_map(fn($r) => $r->value, RoleEnum::approverRoles());
        if (!$staff || ($leave->staff_id !== $staff->id && !in_array($staff->role_id, $approverRoleIds))) {
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
    /**
     * Update the specified leave request (for approvals).
     */
    public function update(ApproveLeaveRequest $request, NotificationService $notificationService, $id)
    {
        $leave = StaffLeave::findOrFail($id);
        $user = Auth::user();
        $staff = $user->staff;

        $action = $request->action;
        $reason = $request->reason;

        // Determine the status based on action using enum
        $statusEnumMap = [
            'approve' => LeaveStatusEnum::APPROVED,
            'reject' => LeaveStatusEnum::REJECTED,
            'recommend' => LeaveStatusEnum::RECOMMENDED,
        ];

        $statusEnum = $statusEnumMap[$action];

        // Create leave action record
        LeaveAction::create([
            'leave_id' => $leave->id,
            'status_id' => $statusEnum->value,
            'actionby' => $staff->id,
            'action_reason' => $reason,
        ]);

        // Notifications
        if ($action === 'recommend') {
            // Notify HR
            $notificationService->notifyHR($leave);
        } else {
            // Notify Staff of final decision
            $notificationService->notifyStaff($leave, $statusEnum->label());
        }

        return redirect('/leaves/' . $id)->with('success', 'Leave request has been ' . $statusEnum->label() . '.');
    }

    /**
     * Cancel a leave request.
     */
    public function cancel(NotificationService $notificationService, $id)
    {
        $leave = StaffLeave::findOrFail($id);
        $user = Auth::user();
        $staff = $user->staff;

        // Only allow canceling own requests
        if (!$staff || $leave->staff_id !== $staff->id) {
            return redirect('/leaves')->with('error', 'You can only cancel your own requests.');
        }

        // Strict Compliance: Can only cancel Pending (Unattended) requests
        // Check the LATEST action status
        $currentStatus = $leave->leaveAction->sortByDesc('created_at')->first();

        if (!$currentStatus || $currentStatus->status_id !== LeaveStatusEnum::UNATTENDED->value) {
            return redirect('/leaves')->with('error', 'You can only cancel Pending leave requests. If approved, please contact HR.');
        }

        LeaveAction::create([
            'leave_id' => $leave->id,
            'status_id' => LeaveStatusEnum::CANCELLED->value,
            'actionby' => $staff->id,
        ]);

        // Notify Supervisor/HR about cancellation
        $notificationService->notifyLeaveCancelled($leave);

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
        $managerRoleIds = [RoleEnum::ADMIN->value, RoleEnum::HR->value];
        if (!$staff || (!in_array($staff->role_id, $managerRoleIds) && $leave->staff_id !== $staff->id)) {
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
        $notificationService = new NotificationService();
        $notificationService->notifySupervisor($leave);
    }

    /**
     * Display personal leave reports for the logged-in staff.
     */
    public function myReports()
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff) {
            return redirect('/dashboard')->with('error', 'Staff profile not found.');
        }

        // Get all leaves for this staff member with statistics
        $leaves = StaffLeave::with(['leaveType', 'leaveAction.leaveStatus'])
            ->where('staff_id', $staff->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_allowance' => $staff->total_leave_days,
            'total_used' => $leaves->where('leaveAction.0.status_id', LeaveStatusEnum::APPROVED->value)->sum('leave_days'),
            'pending' => $leaves->filter(fn($l) => $l->leaveAction->last()?->status_id == LeaveStatusEnum::UNATTENDED->value)->count(),
            'approved' => $leaves->filter(fn($l) => $l->leaveAction->last()?->status_id == LeaveStatusEnum::APPROVED->value)->count(),
            'rejected' => $leaves->filter(fn($l) => in_array($l->leaveAction->last()?->status_id, [LeaveStatusEnum::REJECTED->value, LeaveStatusEnum::DISAPPROVED->value]))->count(),
        ];
        $stats['remaining'] = max(0, $stats['total_allowance'] - $stats['total_used']);

        // Group by leave type for breakdown
        $byType = $leaves->groupBy('leaveType.leave_type_name')->map(fn($group) => $group->sum('leave_days'));

        return view('staff.reports', compact('leaves', 'stats', 'byType', 'staff'));
    }

    /**
     * Export personal leave report as CSV.
     */
    public function exportMyReports()
    {
        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff) {
            return redirect('/dashboard')->with('error', 'Staff profile not found.');
        }

        $leaves = StaffLeave::with(['leaveType', 'leaveAction.leaveStatus'])
            ->where('staff_id', $staff->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'leave_report_' . $staff->staff_number . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($leaves) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Leave Type', 'Start Date', 'End Date', 'Days', 'Status', 'Applied On']);

            foreach ($leaves as $leave) {
                $status = $leave->leaveAction->last()?->leaveStatus?->status_name ?? 'Unknown';
                fputcsv($file, [
                    $leave->leaveType->leave_type_name ?? 'N/A',
                    $leave->start_date,
                    $leave->end_date,
                    $leave->leave_days,
                    $status,
                    $leave->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
