<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class LeaveLevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	$leave_levels = [
    		[
        	'level_name' =>	'Management',
        	'annual_leave_days' =>	36,
        	],
        	[
        	'level_name' =>	'Senior',
        	'annual_leave_days' =>	28,
        	],
        	[
        	'level_name' =>	'Junior',
        	'annual_leave_days' =>	21,
        	]

    	];

        DB::table('leave_levels')->insert($leave_levels);
    }
}
