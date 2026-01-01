<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Holiday;

class LeaveCalculatorService
{
    /**
     * Calculate working days between two dates, excluding weekends and public holidays.
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    public function calculateWorkingDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->gt($end)) {
            return 0;
        }

        // Get all holidays within the range
        // fetching a bit wider range just in case, but strict range is fine
        $holidays = Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        $workingDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            // Check if weekend (Saturday or Sunday)
            $isWeekend = $current->isWeekend();

            // Check if holiday
            $isHoliday = in_array($current->toDateString(), $holidays);

            if (!$isWeekend && !$isHoliday) {
                $workingDays++;
            }

            $current->addDay();
        }

        return $workingDays;
    }
}
