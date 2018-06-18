<?php

use Illuminate\Database\Seeder;

class jobTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
          $jobs = [
        	['job_title' -> '',
        	 'job_description' ->'',],

        ];
        DB::table('jobs')->insert($jobs);
   
    }
}
