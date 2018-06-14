<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        	'id'	=>	1,
        	'email'		=>	'admin@admin.com.gh',
        	'password'	=>	bcrypt('adminpass'),
            'remember_token' => str_random(10)
        ]);
    }
}
