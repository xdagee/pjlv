<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $leave_statuses = [

        	['status_name'=>'Unattended'],

        	['status_name'=>'Approved'],

       		['status_name'=>'Disapproved'],

       		['status_name'=>'Recommended'],

       		['status_name'=>'Rejected'],

            ['status_name'=>'Cancelled']

        ];
        DB::table('leave_statuses')->insert($leave_statuses);
    }
}
