<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StaffLeave;
use App\Staff;
use App\LeaveType;
use App\LeaveStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the reports dashboard.
     */
    public function index(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month');

        // Leave by type summary
        $leaveByType = StaffLeave::select('leave_type_id', DB::raw('SUM(leave_days) as total_days'), DB::raw('COUNT(*) as total_requests'))
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', 2); // Approved
            })
            ->whereYear('start_date', $year)
            ->when($month, function ($q) use ($month) {
                $q->whereMonth('start_date', $month);
            })
            ->groupBy('leave_type_id')
            ->with('leaveType')
            ->get();

        // Monthly leave trend
        $monthlyTrend = StaffLeave::select(
            DB::raw('MONTH(start_date) as month'),
            DB::raw('SUM(leave_days) as total_days')
        )
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', 2);
            })
            ->whereYear('start_date', $year)
            ->groupBy(DB::raw('MONTH(start_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Staff with most leaves
        $topLeaveTakers = StaffLeave::select('staff_id', DB::raw('SUM(leave_days) as total_days'))
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', 2);
            })
            ->whereYear('start_date', $year)
            ->groupBy('staff_id')
            ->orderByDesc('total_days')
            ->limit(10)
            ->with('staff')
            ->get();

        // Overall statistics
        $stats = [
            'total_requests' => StaffLeave::whereYear('start_date', $year)->count(),
            'approved' => StaffLeave::whereYear('start_date', $year)
                ->whereHas('leaveAction', function ($q) {
                    $q->where('status_id', 2);
                })->count(),
            'pending' => StaffLeave::whereYear('start_date', $year)
                ->whereDoesntHave('leaveAction', function ($q) {
                    $q->whereIn('status_id', [2, 3, 5]);
                })->count(),
            'rejected' => StaffLeave::whereYear('start_date', $year)
                ->whereHas('leaveAction', function ($q) {
                    $q->whereIn('status_id', [3, 5]);
                })->count(),
            'total_days' => StaffLeave::whereYear('start_date', $year)
                ->whereHas('leaveAction', function ($q) {
                    $q->where('status_id', 2);
                })->sum('leave_days'),
        ];

        $leaveTypes = LeaveType::all();
        $years = range(Carbon::now()->year - 2, Carbon::now()->year);

        return view('reports', compact('leaveByType', 'monthlyTrend', 'topLeaveTakers', 'stats', 'leaveTypes', 'years', 'year', 'month'));
    }

    /**
     * Export report as CSV.
     */
    public function export(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $leaves = StaffLeave::with(['staff', 'leaveType', 'leaveAction.leaveStatus'])
            ->whereYear('start_date', $year)
            ->orderBy('start_date')
            ->get();

        $filename = "leave_report_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($leaves) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['Staff Name', 'Leave Type', 'Start Date', 'End Date', 'Days', 'Status']);

            foreach ($leaves as $leave) {
                $status = $leave->leaveAction->last()?->leaveStatus->status_name ?? 'Pending';
                fputcsv($file, [
                    ($leave->staff->firstname ?? '') . ' ' . ($leave->staff->lastname ?? ''),
                    $leave->leaveType->leave_type_name ?? 'N/A',
                    $leave->start_date,
                    $leave->end_date,
                    $leave->leave_days,
                    $status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
