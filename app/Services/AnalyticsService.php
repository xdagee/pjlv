<?php

namespace App\Services;

use App\Enums\LeaveStatusEnum;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\StaffLeave;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service for calculating leave analytics with efficient database aggregation.
 */
class AnalyticsService
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Get cache TTL from settings or default.
     */
    private function getCacheTtl(): int
    {
        return $this->settingsService->get('analytics.cache_ttl_minutes', 60) * 60;
    }

    /**
     * Get the SQL expression for extracting month from a date column.
     *
     * @param string $column
     * @return string
     */
    private function monthExpression(string $column): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return "CAST(strftime('%m', {$column}) AS INTEGER)";
        }

        return "MONTH({$column})";
    }

    /**
     * Get the SQL expression for extracting year from a date column.
     *
     * @param string $column
     * @return string
     */
    private function yearExpression(string $column): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return "CAST(strftime('%Y', {$column}) AS INTEGER)";
        }

        return "YEAR({$column})";
    }

    /**
     * Get leave statistics grouped by department.
     *
     * @param int $year
     * @param int|null $month
     * @return \Illuminate\Support\Collection
     */
    public function getLeavesByDepartment(int $year, ?int $month = null)
    {
        $cacheKey = "analytics:leaves_by_dept:{$year}:" . ($month ?? 'all');

        return Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($year, $month) {
            return DB::table('staff_leaves')
                ->join('staff', 'staff_leaves.staff_id', '=', 'staff.id')
                ->join('departments', 'staff.department_id', '=', 'departments.id')
                ->join('leave_actions', 'staff_leaves.id', '=', 'leave_actions.leave_id')
                ->where('leave_actions.status_id', LeaveStatusEnum::APPROVED->value)
                ->whereYear('staff_leaves.start_date', $year)
                ->when($month, function ($query) use ($month) {
                    $query->whereMonth('staff_leaves.start_date', $month);
                })
                ->select(
                    'departments.id as department_id',
                    'departments.name as department_name',
                    DB::raw('COUNT(DISTINCT staff_leaves.id) as total_requests'),
                    DB::raw('SUM(staff_leaves.leave_days) as total_days'),
                    DB::raw('COUNT(DISTINCT staff.id) as unique_staff')
                )
                ->groupBy('departments.id', 'departments.name')
                ->orderBy('total_days', 'desc')
                ->get();
        });
    }

    /**
     * Get leave statistics grouped by leave type.
     *
     * @param int $year
     * @param int|null $month
     * @return \Illuminate\Support\Collection
     */
    public function getLeavesByType(int $year, ?int $month = null)
    {
        $cacheKey = "analytics:leaves_by_type:{$year}:" . ($month ?? 'all');

        return Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($year, $month) {
            return DB::table('staff_leaves')
                ->join('leave_types', 'staff_leaves.leave_type_id', '=', 'leave_types.id')
                ->join('leave_actions', 'staff_leaves.id', '=', 'leave_actions.leave_id')
                ->where('leave_actions.status_id', LeaveStatusEnum::APPROVED->value)
                ->whereYear('staff_leaves.start_date', $year)
                ->when($month, function ($query) use ($month) {
                    $query->whereMonth('staff_leaves.start_date', $month);
                })
                ->select(
                    'leave_types.id as leave_type_id',
                    'leave_types.leave_type_name',
                    'leave_types.leave_duration as allocated_days',
                    DB::raw('COUNT(DISTINCT staff_leaves.id) as total_requests'),
                    DB::raw('SUM(staff_leaves.leave_days) as total_days')
                )
                ->groupBy('leave_types.id', 'leave_types.leave_type_name', 'leave_types.leave_duration')
                ->orderBy('total_days', 'desc')
                ->get();
        });
    }

    /**
     * Get monthly leave trends for a specific year.
     *
     * @param int $year
     * @return \Illuminate\Support\Collection
     */
    public function getMonthlyTrends(int $year)
    {
        $cacheKey = "analytics:monthly_trends:{$year}";

        return Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($year) {
            $monthExpr = $this->monthExpression('staff_leaves.start_date');

            $data = DB::table('staff_leaves')
                ->join('leave_actions', 'staff_leaves.id', '=', 'leave_actions.leave_id')
                ->where('leave_actions.status_id', LeaveStatusEnum::APPROVED->value)
                ->whereYear('staff_leaves.start_date', $year)
                ->select(
                    DB::raw("{$monthExpr} as month"),
                    DB::raw('COUNT(DISTINCT staff_leaves.id) as total_requests'),
                    DB::raw('SUM(staff_leaves.leave_days) as total_days')
                )
                ->groupBy(DB::raw($monthExpr))
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            // Ensure all 12 months are represented
            $result = collect();
            for ($m = 1; $m <= 12; $m++) {
                $result->put($m, (object) [
                    'month' => $m,
                    'month_name' => Carbon::create()->month($m)->format('F'),
                    'total_requests' => $data->get($m)?->total_requests ?? 0,
                    'total_days' => $data->get($m)?->total_days ?? 0,
                ]);
            }

            return $result;
        });
    }

    /**
     * Get yearly leave trends for comparison.
     *
     * @param int $startYear
     * @param int $endYear
     * @return \Illuminate\Support\Collection
     */
    public function getYearlyTrends(int $startYear, int $endYear)
    {
        $cacheKey = "analytics:yearly_trends:{$startYear}:{$endYear}";

        return Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($startYear, $endYear) {
            $yearExpr = $this->yearExpression('staff_leaves.start_date');

            $data = DB::table('staff_leaves')
                ->join('leave_actions', 'staff_leaves.id', '=', 'leave_actions.leave_id')
                ->where('leave_actions.status_id', LeaveStatusEnum::APPROVED->value)
                ->whereYear('staff_leaves.start_date', '>=', $startYear)
                ->whereYear('staff_leaves.start_date', '<=', $endYear)
                ->select(
                    DB::raw("{$yearExpr} as year"),
                    DB::raw('COUNT(DISTINCT staff_leaves.id) as total_requests'),
                    DB::raw('SUM(staff_leaves.leave_days) as total_days')
                )
                ->groupBy(DB::raw($yearExpr))
                ->orderBy('year')
                ->get()
                ->keyBy('year');

            // Ensure all years are represented
            $result = collect();
            for ($y = $startYear; $y <= $endYear; $y++) {
                $result->put($y, (object) [
                    'year' => $y,
                    'total_requests' => $data->get($y)?->total_requests ?? 0,
                    'total_days' => $data->get($y)?->total_days ?? 0,
                ]);
            }

            return $result;
        });
    }

    /**
     * Get overview statistics for the dashboard.
     *
     * @param int $year
     * @param int|null $month
     * @return array
     */
    public function getOverviewStats(int $year, ?int $month = null): array
    {
        $cacheKey = "analytics:overview_stats:{$year}:" . ($month ?? 'all');

        return Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($year, $month) {
            $baseQuery = StaffLeave::query()
                ->whereYear('start_date', $year)
                ->when($month, function ($query) use ($month) {
                    $query->whereMonth('start_date', $month);
                });

            $totalRequests = (clone $baseQuery)->count();

            $approved = (clone $baseQuery)
                ->whereHas('leaveAction', function ($q) {
                    $q->where('status_id', LeaveStatusEnum::APPROVED->value);
                })
                ->count();

            $pending = (clone $baseQuery)
                ->whereDoesntHave('leaveAction', function ($q) {
                    $q->whereIn('status_id', [
                        LeaveStatusEnum::APPROVED->value,
                        LeaveStatusEnum::DISAPPROVED->value,
                        LeaveStatusEnum::REJECTED->value,
                        LeaveStatusEnum::CANCELLED->value,
                    ]);
                })
                ->count();

            $rejected = (clone $baseQuery)
                ->whereHas('leaveAction', function ($q) {
                    $q->whereIn('status_id', [
                        LeaveStatusEnum::DISAPPROVED->value,
                        LeaveStatusEnum::REJECTED->value,
                    ]);
                })
                ->count();

            $totalDays = (clone $baseQuery)
                ->whereHas('leaveAction', function ($q) {
                    $q->where('status_id', LeaveStatusEnum::APPROVED->value);
                })
                ->sum('leave_days');

            // Average days per request (approved only)
            $avgDaysPerRequest = $approved > 0 ? round($totalDays / $approved, 1) : 0;

            // Get department count with active leaves
            $departmentsWithLeaves = DB::table('staff_leaves')
                ->join('staff', 'staff_leaves.staff_id', '=', 'staff.id')
                ->join('leave_actions', 'staff_leaves.id', '=', 'leave_actions.leave_id')
                ->where('leave_actions.status_id', LeaveStatusEnum::APPROVED->value)
                ->whereYear('staff_leaves.start_date', $year)
                ->when($month, function ($query) use ($month) {
                    $query->whereMonth('staff_leaves.start_date', $month);
                })
                ->distinct('staff.department_id')
                ->count('staff.department_id');

            return [
                'total_requests' => $totalRequests,
                'approved' => $approved,
                'pending' => $pending,
                'rejected' => $rejected,
                'total_days' => (int) $totalDays,
                'avg_days_per_request' => $avgDaysPerRequest,
                'departments_with_leaves' => $departmentsWithLeaves,
                'approval_rate' => $totalRequests > 0
                    ? round(($approved / $totalRequests) * 100, 1)
                    : 0,
            ];
        });
    }

    /**
     * Get top leave takers for the period.
     *
     * @param int $year
     * @param int|null $month
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopLeaveTakers(int $year, ?int $month = null, int $limit = 10)
    {
        $cacheKey = "analytics:top_leave_takers:{$year}:" . ($month ?? 'all') . ":{$limit}";

        return Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($year, $month, $limit) {
            return DB::table('staff_leaves')
                ->join('staff', 'staff_leaves.staff_id', '=', 'staff.id')
                ->join('departments', 'staff.department_id', '=', 'departments.id')
                ->join('leave_actions', 'staff_leaves.id', '=', 'leave_actions.leave_id')
                ->where('leave_actions.status_id', LeaveStatusEnum::APPROVED->value)
                ->whereYear('staff_leaves.start_date', $year)
                ->when($month, function ($query) use ($month) {
                    $query->whereMonth('staff_leaves.start_date', $month);
                })
                ->select(
                    'staff.id as staff_id',
                    'staff.firstname',
                    'staff.lastname',
                    'departments.name as department_name',
                    DB::raw('COUNT(staff_leaves.id) as total_requests'),
                    DB::raw('SUM(staff_leaves.leave_days) as total_days')
                )
                ->groupBy('staff.id', 'staff.firstname', 'staff.lastname', 'departments.name')
                ->orderBy('total_days', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear all analytics cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        // Clear cache entries by pattern (requires cache tags or manual clearing)
        // For simplicity, we can use cache tags if available or just let TTL expire
        Cache::flush(); // Note: This clears ALL cache, use tags in production
    }

    /**
     * Get all analytics data for export.
     *
     * @param int $year
     * @param int|null $month
     * @return array
     */
    public function getExportData(int $year, ?int $month = null): array
    {
        return [
            'year' => $year,
            'month' => $month,
            'generated_at' => Carbon::now()->toDateTimeString(),
            'overview' => $this->getOverviewStats($year, $month),
            'by_department' => $this->getLeavesByDepartment($year, $month),
            'by_type' => $this->getLeavesByType($year, $month),
            'monthly_trends' => $this->getMonthlyTrends($year),
            'top_leave_takers' => $this->getTopLeaveTakers($year, $month, $this->getConfiguredTopN()),
        ];
    }

    /**
     * Get configured top N from settings.
     */
    private function getConfiguredTopN(): int
    {
        return $this->settingsService->get('analytics.top_n_takers', 10);
    }
}
