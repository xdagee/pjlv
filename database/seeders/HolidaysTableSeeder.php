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
            ['date' => Carbon::createFromDate($year, 1, 1)->toDateString()],  // New Year
            ['date' => Carbon::createFromDate($year, 3, 6)->toDateString()],  // Independence Day
            ['date' => Carbon::createFromDate($year, 5, 1)->toDateString()],  // Labour Day
            ['date' => Carbon::createFromDate($year, 5, 25)->toDateString()], // Africa Day
            ['date' => Carbon::createFromDate($year, 7, 1)->toDateString()],  // Republic Day
            ['date' => Carbon::createFromDate($year, 9, 21)->toDateString()], // Founder's Day
            ['date' => Carbon::createFromDate($year, 12, 7)->toDateString()], // Constitution Day
            ['date' => Carbon::createFromDate($year, 12, 25)->toDateString()],// Christmas
            ['date' => Carbon::createFromDate($year, 12, 26)->toDateString()],// Boxing Day
        ];

        DB::table('holidays')->insert($holidays);
    }
}
