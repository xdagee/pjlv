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
            LeaveLevelsTableSeeder::class,
            LeaveStatusesTableSeeder::class,
            LeaveTypesTableSeeder::class,
            JobTableSeeder::class,
            RolesTableSeeder::class,
            StaffTableSeeder::class,
            UsersTableSeeder::class,
            LeaveActionsTableSeeder::class,
            HolidaysTableSeeder::class,
        ]);
    }
}
