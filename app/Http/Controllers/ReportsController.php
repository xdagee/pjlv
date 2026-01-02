<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffLeave;
use App\Models\Staff;
use App\Models\LeaveType;
use App\Models\LeaveStatus;
use App\Models\Department;
use App\Enums\LeaveStatusEnum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
                $q->where('status_id', LeaveStatusEnum::APPROVED->value);
            })
            ->whereYear('start_date', $year)
            ->when($month, function ($q) use ($month) {
                $q->whereMonth('start_date', $month);
            })
            ->groupBy('leave_type_id')
            ->with('leaveType')
            ->get();

        // Leave by Department
        $leaveByDepartment = StaffLeave::select('staff.department_id', DB::raw('SUM(staff_leaves.leave_days) as total_days'), DB::raw('COUNT(*) as total_requests'))
            ->join('staff', 'staff_leaves.staff_id', '=', 'staff.id')
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', LeaveStatusEnum::APPROVED->value);
            })
            ->whereYear('staff_leaves.start_date', $year)
            ->when($month, function ($q) use ($month) {
                $q->whereMonth('staff_leaves.start_date', $month);
            })
            ->groupBy('staff.department_id')
            ->get()
            ->map(function ($item) {
                $item->department = Department::find($item->department_id);
                return $item;
            });

        // Monthly leave trend
        $monthlyTrend = StaffLeave::select(
            DB::raw('MONTH(start_date) as month'),
            DB::raw('SUM(leave_days) as total_days')
        )
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', LeaveStatusEnum::APPROVED->value);
            })
            ->whereYear('start_date', $year)
            ->groupBy(DB::raw('MONTH(start_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Staff with most leaves
        $topLeaveTakers = StaffLeave::select('staff_id', DB::raw('SUM(leave_days) as total_days'))
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', LeaveStatusEnum::APPROVED->value);
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
                    $q->where('status_id', LeaveStatusEnum::APPROVED->value);
                })->count(),
            'pending' => StaffLeave::whereYear('start_date', $year)
                ->whereDoesntHave('leaveAction', function ($q) {
                    $q->whereIn('status_id', [
                        LeaveStatusEnum::APPROVED->value,
                        LeaveStatusEnum::DISAPPROVED->value,
                        LeaveStatusEnum::REJECTED->value,
                    ]);
                })->count(),
            'rejected' => StaffLeave::whereYear('start_date', $year)
                ->whereHas('leaveAction', function ($q) {
                    $q->whereIn('status_id', [
                        LeaveStatusEnum::DISAPPROVED->value,
                        LeaveStatusEnum::REJECTED->value,
                    ]);
                })->count(),
            'total_days' => StaffLeave::whereYear('start_date', $year)
                ->whereHas('leaveAction', function ($q) {
                    $q->where('status_id', LeaveStatusEnum::APPROVED->value);
                })->sum('leave_days'),
        ];

        $leaveTypes = LeaveType::all();
        $departments = Department::all();
        $years = range(Carbon::now()->year - 2, Carbon::now()->year);

        // Use admin view for /admin/reports, staff view for /reports
        $view = request()->is('admin/*') ? 'admin.reports.index' : 'staff.reports.index';
        return view($view, compact('leaveByType', 'leaveByDepartment', 'monthlyTrend', 'topLeaveTakers', 'stats', 'leaveTypes', 'departments', 'years', 'year', 'month'));
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
            fputcsv($file, ['Staff Name', 'Department', 'Leave Type', 'Start Date', 'End Date', 'Days', 'Status']);

            foreach ($leaves as $leave) {
                $status = $leave->leaveAction->last()?->leaveStatus->status_name ?? 'Pending';
                fputcsv($file, [
                    ($leave->staff->firstname ?? '') . ' ' . ($leave->staff->lastname ?? ''),
                    $leave->staff->department->name ?? 'N/A',
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

    /**
     * Export report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        // Fetch data for PDF
        $leaveByType = StaffLeave::select('leave_type_id', DB::raw('SUM(leave_days) as total_days'), DB::raw('COUNT(*) as total_requests'))
            ->whereHas('leaveAction', fn($q) => $q->where('status_id', LeaveStatusEnum::APPROVED->value))
            ->whereYear('start_date', $year)
            ->groupBy('leave_type_id')
            ->with('leaveType')
            ->get();

        $leaveByDepartment = StaffLeave::select('staff.department_id', DB::raw('SUM(staff_leaves.leave_days) as total_days'), DB::raw('COUNT(*) as total_requests'))
            ->join('staff', 'staff_leaves.staff_id', '=', 'staff.id')
            ->whereHas('leaveAction', fn($q) => $q->where('status_id', LeaveStatusEnum::APPROVED->value))
            ->whereYear('staff_leaves.start_date', $year)
            ->groupBy('staff.department_id')
            ->get()
            ->map(fn($item) => tap($item, fn($i) => $i->department = Department::find($i->department_id)));

        $topLeaveTakers = StaffLeave::select('staff_id', DB::raw('SUM(leave_days) as total_days'))
            ->whereHas('leaveAction', fn($q) => $q->where('status_id', LeaveStatusEnum::APPROVED->value))
            ->whereYear('start_date', $year)
            ->groupBy('staff_id')
            ->orderByDesc('total_days')
            ->limit(10)
            ->with('staff')
            ->get();

        $stats = [
            'total_requests' => StaffLeave::whereYear('start_date', $year)->count(),
            'approved' => StaffLeave::whereYear('start_date', $year)->whereHas('leaveAction', fn($q) => $q->where('status_id', LeaveStatusEnum::APPROVED->value))->count(),
            'total_days' => StaffLeave::whereYear('start_date', $year)->whereHas('leaveAction', fn($q) => $q->where('status_id', LeaveStatusEnum::APPROVED->value))->sum('leave_days'),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf', compact('leaveByType', 'leaveByDepartment', 'topLeaveTakers', 'stats', 'year'));

        return $pdf->download("leave_report_{$year}.pdf");
    }
}
