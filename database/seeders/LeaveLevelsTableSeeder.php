<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveLevelsTableSeeder extends Seeder
{
    public function run(): void
    {
        $leave_levels = [
            ['level_name' => 'Management', 'annual_leave_days' => 36],
            ['level_name' => 'Senior', 'annual_leave_days' => 28],
            ['level_name' => 'Junior', 'annual_leave_days' => 21],
            ['level_name' => 'NSS', 'annual_leave_days' => 30],
            ['level_name' => 'Intern', 'annual_leave_days' => 15],
        ];

        foreach ($leave_levels as $level) {
            DB::table('leave_levels')->updateOrInsert(
                ['level_name' => $level['level_name']],
                $level
            );
        }
    }
}
