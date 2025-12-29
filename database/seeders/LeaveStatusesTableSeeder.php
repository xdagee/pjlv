<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveStatusesTableSeeder extends Seeder
{
    public function run(): void
    {
        $leave_statuses = [
            ['status_name' => 'Unattended'],
            ['status_name' => 'Approved'],
            ['status_name' => 'Disapproved'],
            ['status_name' => 'Recommended'],
            ['status_name' => 'Rejected'],
            ['status_name' => 'Cancelled']
        ];

        DB::table('leave_statuses')->insert($leave_statuses);
    }
}
