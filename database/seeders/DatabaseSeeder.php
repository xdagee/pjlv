<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
                // 1. Reference data - must be seeded first (no dependencies)
            LeaveLevelsTableSeeder::class,
            LeaveStatusesTableSeeder::class,
            LeaveTypesTableSeeder::class,
            JobTableSeeder::class,
            RolesTableSeeder::class,
            DepartmentsTableSeeder::class,
            SystemSettingsSeeder::class,

                // 2. System admin account (standalone, not linked to staff)
            AdminAccountSeeder::class,

                // 3. User data seeders (depend on reference data)
            StaffTableSeeder::class,
            UsersTableSeeder::class,
            LeaveActionsTableSeeder::class,
            HolidaysTableSeeder::class,
        ]);
    }
}

