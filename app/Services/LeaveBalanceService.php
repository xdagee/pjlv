<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\LeaveType;
use App\Enums\LeaveStatusEnum;
use Illuminate\Support\Facades\Cache;

class LeaveBalanceService
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    protected const CACHE_TTL = 3600;

    /**
     * Get the remaining leave balance for a staff member
     *
     * @param int $staffId
     * @param int|null $leaveTypeId
     * @return int
     */
    public function getBalance(int $staffId, ?int $leaveTypeId = null): int
    {
        $cacheKey = $this->getBalanceCacheKey($staffId, $leaveTypeId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($staffId, $leaveTypeId) {
            $staff = Staff::find($staffId);
            if (!$staff) {
                return 0;
            }

            $totalAllowance = $staff->total_leave_days;
            $usedDays = $this->getUsedDays($staffId, $leaveTypeId);

            return max(0, $totalAllowance - $usedDays);
        });
    }

    /**
     * Get the number of leave days used by a staff member
     *
     * @param int $staffId
     * @param int|null $leaveTypeId
     * @return int
     */
    public function getUsedDays(int $staffId, ?int $leaveTypeId = null): int
    {
        $query = StaffLeave::where('staff_id', $staffId)
            ->whereHas('leaveAction', function ($q) {
                // strict compliance: Pending (Unattended) requests must deduct temporarily
                $q->whereIn('status_id', [
                    LeaveStatusEnum::APPROVED->value,
                    LeaveStatusEnum::UNATTENDED->value
                ]);
            });

        if ($leaveTypeId) {
            $query->where('leave_type_id', $leaveTypeId);
        }

        return (int) $query->sum('leave_days');
    }

    /**
     * Check if staff member can apply for leave
     *
     * @param int $staffId
     * @param int $days
     * @return bool
     */
    public function canApply(int $staffId, int $days): bool
    {
        $balance = $this->getBalance($staffId);
        return $balance >= $days;
    }

    /**
     * Get breakdown of leave balance by type
     *
     * @param int $staffId
     * @return array
     */
    public function getBalanceBreakdown(int $staffId): array
    {
        $cacheKey = "leave_breakdown_{$staffId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($staffId) {
            $staff = Staff::find($staffId);
            if (!$staff) {
                return [
                    'total_allowance' => 0,
                    'total_used' => 0,
                    'remaining' => 0,
                    'by_type' => [],
                ];
            }

            $leaveTypes = LeaveType::all();
            $breakdown = [];

            foreach ($leaveTypes as $leaveType) {
                $usedDays = $this->getUsedDays($staffId, $leaveType->id);
                $allocated = $leaveType->leave_duration ?? 0;
                $remaining = max(0, $allocated - $usedDays);

                $breakdown[$leaveType->leave_type_name] = [
                    'allocated' => $allocated,
                    'used' => $usedDays,
                    'remaining' => $remaining,
                    'type_id' => $leaveType->id,
                ];
            }

            $totalUsed = array_sum(array_column($breakdown, 'used'));

            return [
                'total_allowance' => $staff->total_leave_days,
                'total_used' => $totalUsed,
                'remaining' => max(0, $staff->total_leave_days - $totalUsed),
                'by_type' => $breakdown,
            ];
        });
    }

    /**
     * Check for overlapping leave dates
     *
     * @param int $staffId
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeLeaveId
     * @return bool
     */
    public function hasOverlappingLeave(int $staffId, string $startDate, string $endDate, ?int $excludeLeaveId = null): bool
    {
        $query = StaffLeave::where('staff_id', $staffId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->whereHas('leaveAction', function ($q) {
                $q->whereIn('status_id', [
                    LeaveStatusEnum::UNATTENDED->value,
                    LeaveStatusEnum::APPROVED->value,
                    LeaveStatusEnum::RECOMMENDED->value,
                ]);
            });

        if ($excludeLeaveId) {
            $query->where('id', '!=', $excludeLeaveId);
        }

        return $query->exists();
    }

    /**
     * Clear cache for a staff member's leave balance.
     *
     * @param int $staffId
     * @return void
     */
    public function clearCache(int $staffId): void
    {
        // Clear all possible cache keys for this staff
        Cache::forget($this->getBalanceCacheKey($staffId, null));
        Cache::forget("leave_breakdown_{$staffId}");

        // Clear type-specific caches
        $leaveTypes = LeaveType::all();
        foreach ($leaveTypes as $leaveType) {
            Cache::forget($this->getBalanceCacheKey($staffId, $leaveType->id));
        }
    }

    /**
     * Get cache key for balance.
     */
    protected function getBalanceCacheKey(int $staffId, ?int $leaveTypeId): string
    {
        return "leave_balance_{$staffId}" . ($leaveTypeId ? "_{$leaveTypeId}" : "");
    }
}
