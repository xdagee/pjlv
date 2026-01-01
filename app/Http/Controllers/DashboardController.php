<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffLeave;
use App\Models\Staff;
use App\Models\Holiday;
use App\Models\LeaveStatus;
use App\Enums\LeaveStatusEnum;
use App\Enums\RoleEnum;
use Carbon\Carbon;
use App\Services\LeaveBalanceService;

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
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(LeaveBalanceService $balanceService)
    {
        $user = Auth::user();
        $staff = $user->staff;

        // Get pending leave requests (for HR/Admin - all pending, for others - their own)
        if ($staff && in_array($staff->role_id, [RoleEnum::ADMIN->value, RoleEnum::HR->value])) {
            $pendingLeaves = StaffLeave::whereHas('leaveAction', function ($q) {
                $q->where('status_id', LeaveStatusEnum::UNATTENDED->value);
            })->orWhereDoesntHave('leaveAction')->count();
        } else {
            $pendingLeaves = $staff ? StaffLeave::where('staff_id', $staff->id)
                ->whereDoesntHave('leaveAction', function ($q) {
                    $q->whereIn('status_id', [
                        LeaveStatusEnum::APPROVED->value,
                        LeaveStatusEnum::DISAPPROVED->value,
                    ]);
                })->count() : 0;
        }

        // Get today's absentees (staff on leave today)
        $today = Carbon::today()->toDateString();
        $staffOnLeaveToday = StaffLeave::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', LeaveStatusEnum::APPROVED->value);
            })->count();

        // Get user's leave balance breakdown via Service
        $balanceBreakdown = $staff ? $balanceService->getBalanceBreakdown($staff->id) : [
            'total_allowance' => 0,
            'total_used' => 0,
            'remaining' => 0,
            'by_type' => []
        ];

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
            'balanceBreakdown',
            'upcomingHolidays',
            'recentLeaves',
            'totalStaff',
            'staff'
        ));
    }
}
