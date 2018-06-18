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
<<<<<<< HEAD
        $this->call(StaffTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(LeaveStatusTableSeeder::class);
        
=======
        $this->call(StaffsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
>>>>>>> c380fb589c0ec27f25f4962c2c90243f5fef6602
    }
}
