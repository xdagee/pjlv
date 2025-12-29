<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Admin', 'role_status' => 1],
            ['role_name' => 'HR', 'role_status' => 1],
            ['role_name' => 'DG', 'role_status' => 1],
            ['role_name' => 'Director', 'role_status' => 1],
            ['role_name' => 'Normal', 'role_status' => 1]
        ];

        DB::table('roles')->insert($roles);
    }
}
