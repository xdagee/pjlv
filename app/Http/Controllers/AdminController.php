<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\Job;
use App\Models\Department;
use App\Models\LeaveType;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->middleware(['auth', 'superadmin']);
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the unified admin dashboard with analytics.
     */
    public function index(Request $request)
    {
        // Filter parameters
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month');

        // Basic Dashboard Stats
        $totalStaff = Staff::count();
        $staffOnLeave = StaffLeave::approved()->onDate(now()->toDateString())->count();
        $pendingLeaves = StaffLeave::pending()->count();
        $totalJobs = Job::count();

        // Analytics Data
        $stats = $this->analyticsService->getOverviewStats($year, $month);
        $leavesByDepartment = $this->analyticsService->getLeavesByDepartment($year, $month);
        $leavesByType = $this->analyticsService->getLeavesByType($year, $month);
        $monthlyTrends = $this->analyticsService->getMonthlyTrends($year);
        $yearlyTrends = $this->analyticsService->getYearlyTrends($year - 2, $year);
        $topLeaveTakers = $this->analyticsService->getTopLeaveTakers($year, $month);

        // Filter options
        $departments = Department::orderBy('name')->get();
        $leaveTypes = LeaveType::all();
        $years = range(Carbon::now()->year - 4, Carbon::now()->year);

        return view('admin.dashboard', compact(
            // Basic stats
            'totalStaff',
            'staffOnLeave',
            'pendingLeaves',
            'totalJobs',
            // Analytics data
            'stats',
            'leavesByDepartment',
            'leavesByType',
            'monthlyTrends',
            'yearlyTrends',
            'topLeaveTakers',
            // Filter options
            'departments',
            'leaveTypes',
            'years',
            'year',
            'month'
        ));
    }
}
