<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $leave_types = [
            ['leave_type_name' => 'Annual Leave', 'leave_duration' => '0'],
            ['leave_type_name' => 'Sick Leave', 'leave_duration' => '365'],
            ['leave_type_name' => 'Maternity Leave', 'leave_duration' => '84'],
            ['leave_type_name' => 'Paternity Leave', 'leave_duration' => '5'],
            ['leave_type_name' => 'Examinations Leave', 'leave_duration' => '10'],
            ['leave_type_name' => 'Sports Leave', 'leave_duration' => '5']
        ];

        foreach ($leave_types as $type) {
            DB::table('leave_types')->updateOrInsert(
                ['leave_type_name' => $type['leave_type_name']],
                $type
            );
        }
    }
}
