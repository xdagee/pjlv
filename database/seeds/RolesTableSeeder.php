<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
    		[
        	'role_name' =>	'Admin',
        	'role_status' =>	true,
        	],
        	[
        	'role_name' =>	'HR',
        	'role_status' =>	true,
        	],
        	[
        	'role_name' =>	'DG',
        	'role_status' =>	true,
        	],
        	[
        	'role_name' =>	'Director',
        	'role_status' =>	true,
        	],
        	[
        	'role_name' =>	'Normal',
        	'role_status' =>	true,
        	]

    	];

        DB::table('roles')->insert($roles);
    }
}
