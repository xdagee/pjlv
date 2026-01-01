<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'IT', 'description' => 'Information Technology and Systems'],
            ['name' => 'Human Resources', 'description' => 'Staff welfare and recruitment'],
            ['name' => 'Finance', 'description' => 'Accounting and Payroll'],
            ['name' => 'Operations', 'description' => 'Day-to-day business operations'],
            ['name' => 'Administration', 'description' => 'General management and support'],
        ];

        foreach ($departments as $dept) {
            \App\Models\Department::firstOrCreate(
                ['name' => $dept['name']],
                $dept
            );
        }
    }
}
