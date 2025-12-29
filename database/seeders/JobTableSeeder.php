<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobTableSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            ['job_title' => 'Software Developer', 'job_description' => 'Develops and maintains software applications', 'is_multiple_staff' => 1],
            ['job_title' => 'Project Manager', 'job_description' => 'Manages project timelines and deliverables', 'is_multiple_staff' => 1],
            ['job_title' => 'HR Manager', 'job_description' => 'Handles human resources operations', 'is_multiple_staff' => 0],
            ['job_title' => 'Accountant', 'job_description' => 'Manages financial records and transactions', 'is_multiple_staff' => 1],
            ['job_title' => 'System Administrator', 'job_description' => 'Maintains IT systems and infrastructure', 'is_multiple_staff' => 1],
        ];

        DB::table('jobs')->insert($jobs);
    }
}
