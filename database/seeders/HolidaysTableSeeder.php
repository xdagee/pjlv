<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HolidaysTableSeeder extends Seeder
{
    public function run(): void
    {
        $year = Carbon::now()->year;

        $holidays = [
            ['name' => 'New Year', 'date' => Carbon::createFromDate($year, 1, 1)->toDateString()],
            ['name' => 'Independence Day', 'date' => Carbon::createFromDate($year, 3, 6)->toDateString()],
            ['name' => 'Labour Day', 'date' => Carbon::createFromDate($year, 5, 1)->toDateString()],
            ['name' => 'Africa Day', 'date' => Carbon::createFromDate($year, 5, 25)->toDateString()],
            ['name' => 'Republic Day', 'date' => Carbon::createFromDate($year, 7, 1)->toDateString()],
            ['name' => 'Founders Day', 'date' => Carbon::createFromDate($year, 9, 21)->toDateString()],
            ['name' => 'Farmer\'s Day', 'date' => Carbon::createFromDate($year, 12, 6)->toDateString()], // Approx
            ['name' => 'Christmas Day', 'date' => Carbon::createFromDate($year, 12, 25)->toDateString()],
            ['name' => 'Boxing Day', 'date' => Carbon::createFromDate($year, 12, 26)->toDateString()],
        ];

        foreach ($holidays as $holiday) {
            DB::table('holidays')->updateOrInsert(
                ['date' => $holiday['date']],
                $holiday
            );
        }
    }
}
