<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Staff;
use App\Models\Role;
use App\Models\Department;
use Faker\Factory as Faker;

class HRScenarioSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // 1. Ensure HR Role exists
        $hrRole = Role::updateOrCreate(['id' => 2], ['role_name' => 'HR', 'role_status' => 1]);
        $roleNormal = Role::updateOrCreate(['id' => 5], ['role_name' => 'Normal', 'role_status' => 1]);

        // 2. Ensure Departments exist
        $hrDept = Department::firstOrCreate(['name' => 'Human Resources'], ['description' => 'HR Department']);
        $itDept = Department::firstOrCreate(['name' => 'Information Technology'], ['description' => 'IT Department']);

        // 3. Create or Get HR User
        $hrEmail = 'hr_manager@pjlv.test';
        $hrUser = User::where('email', $hrEmail)->first();

        // Disable FK checks just in case
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        if (!$hrUser) {
            $hrId = max(User::max('id') ?? 0, Staff::max('id') ?? 0) + 1;

            Staff::forceCreate([
                'id' => $hrId,
                'staff_number' => 'HR-001',
                'title' => 'Mrs',
                'firstname' => 'Helen',
                'lastname' => 'Manager',
                'dob' => '1980-01-01',
                'role_id' => $hrRole->id,
                'department_id' => $hrDept->id,
                'is_active' => 1,
                'gender' => 0,
                'mobile_number' => '0777777777',
                'date_joined' => now(),
                'total_leave_days' => 20
            ]);

            $hrUser = User::forceCreate([
                'id' => $hrId,
                'email' => $hrEmail,
                'password' => Hash::make('password'),
            ]);

            $this->command->info("Created HR User: {$hrEmail} / password");
        } else {
            $this->command->info("Using existing HR User: {$hrEmail}");
        }

        // 4. Create 10 Staff Members
        $this->command->info("Creating 10 staff members...");

        for ($i = 1; $i <= 10; $i++) {
            $sId = max(User::max('id') ?? 0, Staff::max('id') ?? 0) + 1;
            $email = "staff_gen_{$i}_" . uniqid() . "@pjlv.test";

            Staff::forceCreate([
                'id' => $sId,
                'staff_number' => 'STF-G' . str_pad($i, 3, '0', STR_PAD_LEFT) . rand(10, 99),
                'title' => $faker->title,
                'firstname' => $faker->firstName,
                'lastname' => $faker->lastName,
                'dob' => $faker->date('Y-m-d', '-20 years'),
                'role_id' => $roleNormal->id,
                'department_id' => $itDept->id,
                'supervisor_id' => $hrUser->id,
                'is_active' => 1,
                'gender' => $faker->boolean,
                'mobile_number' => $faker->numerify('07########'),
                'date_joined' => now(),
                'total_leave_days' => 20
            ]);

            User::forceCreate([
                'id' => $sId,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
        }

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        $this->command->info("Created 10 staff members successfully.");
    }
}
