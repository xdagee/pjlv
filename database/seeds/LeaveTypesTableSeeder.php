<?php

use Illuminate\Database\Seeder;

class LeaveTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('leave_types')->insert([
        	['leave_type_name' -> 'Sick Leave',
        	'leave_duration' -> '365',],

        	['leave_type_name' -> 'Maternity Leave',
        	'leave_duration' -> '60',],

        	['leave_type_name' -> 'Paternity Leave',
        	'leave_duration' -> '5',],

        	['leave_type_name' -> 'Examinations Leave',
        	'leave_duration' -> '10',],

        	['leave_type_name' -> 'Sports Leave',
        	'leave_duration' -> '5',],

    }
}
