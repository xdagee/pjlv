<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffTableSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        DB::table('staff')->insert([
            'staff_number' => '11111111',
            'title' => 'Mr',
            'firstname' => 'Admin',
            'lastname' => 'System',
            'othername' => 'Administrator',
            'dob' => '1995-06-10',
            'mobile_number' => '+233202996677',
            'gender' => 1,
            'is_active' => 1,
            'date_joined' => '2018-06-14',
            'leave_level_id' => 1,
            'total_leave_days' => 36,
            'role_id' => 1
        ]);

        // HR Manager
        DB::table('staff')->insert([
            'staff_number' => '22222222',
            'title' => 'Ms',
            'firstname' => 'HR',
            'lastname' => 'Manager',
            'othername' => '',
            'dob' => '1990-03-15',
            'mobile_number' => '+233203334455',
            'gender' => 2,
            'is_active' => 1,
            'date_joined' => '2019-01-10',
            'leave_level_id' => 1,
            'total_leave_days' => 36,
            'role_id' => 2
        ]);

        // Normal Employee
        DB::table('staff')->insert([
            'staff_number' => '33333333',
            'title' => 'Mr',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'othername' => 'Employee',
            'dob' => '1992-08-20',
            'mobile_number' => '+233205556677',
            'gender' => 1,
            'is_active' => 1,
            'date_joined' => '2020-02-01',
            'leave_level_id' => 3,
            'total_leave_days' => 21,
            'role_id' => 5
        ]);
    }
}
