<?php

namespace App\Services;

use App\Staff;
use App\StaffLeave;
use App\LeaveType;

class LeaveBalanceService
{
    /**
     * Get the remaining leave balance for a staff member
     *
     * @param int $staffId
     * @param int|null $leaveTypeId
     * @return int
     */
    public function getBalance(int $staffId, ?int $leaveTypeId = null): int
    {
        $staff = Staff::find($staffId);
        if (!$staff) {
            return 0;
        }

        $totalAllowance = $staff->total_leave_days;
        $usedDays = $this->getUsedDays($staffId, $leaveTypeId);

        return max(0, $totalAllowance - $usedDays);
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
                $q->where('status_id', 2); // Approved status
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
        return $this->getBalance($staffId) >= $days;
    }

    /**
     * Get leave balance breakdown by leave type
     *
     * @param int $staffId
     * @return array
     */
    public function getBalanceBreakdown(int $staffId): array
    {
        $staff = Staff::find($staffId);
        if (!$staff) {
            return [];
        }

        $leaveTypes = LeaveType::all();
        $breakdown = [];

        foreach ($leaveTypes as $type) {
            $used = $this->getUsedDays($staffId, $type->id);
            $breakdown[] = [
                'type' => $type->leave_type_name,
                'type_id' => $type->id,
                'used' => $used,
                'duration_limit' => $type->leave_duration,
            ];
        }

        return [
            'total_allowance' => $staff->total_leave_days,
            'total_used' => $this->getUsedDays($staffId),
            'remaining' => $this->getBalance($staffId),
            'by_type' => $breakdown,
        ];
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
                $q->whereIn('status_id', [1, 2, 4]); // Pending, Approved, Recommended
            });

        if ($excludeLeaveId) {
            $query->where('id', '!=', $excludeLeaveId);
        }

        return $query->exists();
    }
}
