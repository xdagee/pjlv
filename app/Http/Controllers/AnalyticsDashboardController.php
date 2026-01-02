<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Models\Department;
use App\Models\LeaveType;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Admin-only controller for leave analytics dashboard.
 */
class AnalyticsDashboardController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->middleware('auth');
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = $request->input('month') ? (int) $request->input('month') : null;

        // Get all analytics data
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

        return view('admin.analytics.index', compact(
            'stats',
            'leavesByDepartment',
            'leavesByType',
            'monthlyTrends',
            'yearlyTrends',
            'topLeaveTakers',
            'departments',
            'leaveTypes',
            'years',
            'year',
            'month'
        ));
    }

    /**
     * Export analytics data as CSV.
     */
    public function exportCsv(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = $request->input('month') ? (int) $request->input('month') : null;

        $data = $this->analyticsService->getExportData($year, $month);

        $filename = "leave_analytics_{$year}";
        if ($month) {
            $filename .= "_" . Carbon::create()->month($month)->format('F');
        }
        $filename .= ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report Header
            fputcsv($file, ['Leave Analytics Report']);
            fputcsv($file, ['Generated:', $data['generated_at']]);
            fputcsv($file, ['Year:', $data['year']]);
            if ($data['month']) {
                fputcsv($file, ['Month:', Carbon::create()->month($data['month'])->format('F')]);
            }
            fputcsv($file, []);

            // Overview Statistics
            fputcsv($file, ['=== OVERVIEW STATISTICS ===']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Requests', $data['overview']['total_requests']]);
            fputcsv($file, ['Approved', $data['overview']['approved']]);
            fputcsv($file, ['Pending', $data['overview']['pending']]);
            fputcsv($file, ['Rejected', $data['overview']['rejected']]);
            fputcsv($file, ['Total Days Used', $data['overview']['total_days']]);
            fputcsv($file, ['Avg Days/Request', $data['overview']['avg_days_per_request']]);
            fputcsv($file, ['Approval Rate (%)', $data['overview']['approval_rate']]);
            fputcsv($file, []);

            // Leave by Department
            fputcsv($file, ['=== LEAVES BY DEPARTMENT ===']);
            fputcsv($file, ['Department', 'Requests', 'Days Used', 'Unique Staff']);
            foreach ($data['by_department'] as $dept) {
                fputcsv($file, [
                    $dept->department_name,
                    $dept->total_requests,
                    $dept->total_days,
                    $dept->unique_staff,
                ]);
            }
            fputcsv($file, []);

            // Leave by Type
            fputcsv($file, ['=== LEAVES BY TYPE ===']);
            fputcsv($file, ['Leave Type', 'Allocated Days', 'Requests', 'Days Used']);
            foreach ($data['by_type'] as $type) {
                fputcsv($file, [
                    $type->leave_type_name,
                    $type->allocated_days,
                    $type->total_requests,
                    $type->total_days,
                ]);
            }
            fputcsv($file, []);

            // Monthly Trends
            fputcsv($file, ['=== MONTHLY TRENDS ===']);
            fputcsv($file, ['Month', 'Requests', 'Days Used']);
            foreach ($data['monthly_trends'] as $trend) {
                fputcsv($file, [
                    $trend->month_name,
                    $trend->total_requests,
                    $trend->total_days,
                ]);
            }
            fputcsv($file, []);

            // Top Leave Takers
            fputcsv($file, ['=== TOP LEAVE TAKERS ===']);
            fputcsv($file, ['Staff Name', 'Department', 'Requests', 'Days Used']);
            foreach ($data['top_leave_takers'] as $staff) {
                fputcsv($file, [
                    $staff->firstname . ' ' . $staff->lastname,
                    $staff->department_name,
                    $staff->total_requests,
                    $staff->total_days,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export analytics data as PDF.
     */
    public function exportPdf(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = $request->input('month') ? (int) $request->input('month') : null;

        $data = $this->analyticsService->getExportData($year, $month);

        $filename = "leave_analytics_{$year}";
        if ($month) {
            $filename .= "_" . Carbon::create()->month($month)->format('F');
        }
        $filename .= ".pdf";

        $pdf = Pdf::loadView('admin.analytics.pdf', [
            'data' => $data,
            'year' => $year,
            'month' => $month,
            'monthName' => $month ? Carbon::create()->month($month)->format('F') : null,
        ]);

        return $pdf->download($filename);
    }
}
