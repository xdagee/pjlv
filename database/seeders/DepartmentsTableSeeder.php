<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Department;

class DepartmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Business Development', 'description' => 'Identifying new business opportunities and building strategic partnerships.'],
            ['name' => 'Engineering', 'description' => 'Design, development, and maintenance of technical systems and software.'],
            ['name' => 'Product Development', 'description' => 'Overseeing the creation and improvement of company products.'],
            ['name' => 'Research Development', 'description' => 'Innovating and testing new technologies and methodologies.'],
            ['name' => 'Finance', 'description' => 'Managing financial planning, record-keeping, and reporting.'],
            ['name' => 'Training', 'description' => 'Employee development, workshops, and continuous learning programs.'],
            ['name' => 'Business Administration', 'description' => 'Overseeing daily operations and administrative support functions.'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['name' => $dept['name']], $dept);
        }
    }
}
