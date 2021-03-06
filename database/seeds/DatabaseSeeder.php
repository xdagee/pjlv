<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LeaveLevelsTableSeeder::class);
        $this->call(LeaveStatusesTableSeeder::class);
        $this->call(LeaveTypesTableSeeder::class);
        $this->call(JobTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(StaffTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(LeaveActionsTableSeeder::class);
        $this->call(HolidaysTableSeeder::class);
    }
}
