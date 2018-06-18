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
        $this->call(RolesTableSeeder::class);
        $this->call(StaffsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
