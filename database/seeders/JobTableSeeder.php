<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobTableSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            ['job_title' => 'Chief Executive Officer (CEO)', 'job_description' => 'Overall leadership, product vision, software & platform development', 'is_multiple_staff' => 0],
            ['job_title' => 'Chief Evangelist Officer (CEOv)', 'job_description' => 'Advocacy, community engagement, mentorship, storytelling', 'is_multiple_staff' => 0],
            ['job_title' => 'Chief Business Development Officer (CBDO)', 'job_description' => 'Market expansion, partnerships, sales strategy', 'is_multiple_staff' => 0],
            ['job_title' => 'Operations Lead', 'job_description' => 'Cross-functional leadership role that ensures smooth coordination and execution of organizational activities across departments', 'is_multiple_staff' => 0],

        ];

        foreach ($jobs as $job) {
            DB::table('jobs')->updateOrInsert(
                ['job_title' => $job['job_title']],
                $job
            );
        }
    }
}
