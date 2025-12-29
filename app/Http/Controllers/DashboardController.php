<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\StaffLeave;
use App\Staff;
use App\Holiday;
use App\LeaveStatus;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard with real leave data.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $staff = $user->staff;

        // Get pending leave requests (for HR/Admin - all pending, for others - their own)
        $pendingStatusId = LeaveStatus::where('status_name', 'Unattended')->first()?->id ?? 1;

        if ($staff && in_array($staff->role_id, [1, 2])) { // Admin or HR
            $pendingLeaves = StaffLeave::whereHas('leaveAction', function ($q) use ($pendingStatusId) {
                $q->where('status_id', $pendingStatusId);
            })->orWhereDoesntHave('leaveAction')->count();
        } else {
            $pendingLeaves = $staff ? StaffLeave::where('staff_id', $staff->id)
                ->whereDoesntHave('leaveAction', function ($q) {
                    $q->whereIn('status_id', [2, 3]); // Approved or Disapproved
                })->count() : 0;
        }

        // Get today's absentees (staff on leave today)
        $today = Carbon::today()->toDateString();
        $staffOnLeaveToday = StaffLeave::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', 2); // Approved
            })->count();

        // Get user's leave balance
        $leaveBalance = $staff ? $staff->total_leave_days : 0;
        $usedLeaveDays = $staff ? StaffLeave::where('staff_id', $staff->id)
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', 2); // Approved
            })->sum('leave_days') : 0;
        $remainingLeave = max(0, $leaveBalance - $usedLeaveDays);

        // Get upcoming holidays (next 30 days)
        $upcomingHolidays = Holiday::where('date', '>=', Carbon::today())
            ->where('date', '<=', Carbon::today()->addDays(30))
            ->orderBy('date')
            ->get();

        // Get recent leave requests (last 5)
        $recentLeaves = $staff ? StaffLeave::where('staff_id', $staff->id)
            ->with(['leaveType', 'leaveAction.leaveStatus'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get() : collect();

        // Total staff count
        $totalStaff = Staff::where('is_active', 1)->count();

        return view('dashboard', compact(
            'pendingLeaves',
            'staffOnLeaveToday',
            'leaveBalance',
            'usedLeaveDays',
            'remainingLeave',
            'upcomingHolidays',
            'recentLeaves',
            'totalStaff',
            'staff'
        ));
    }
}
