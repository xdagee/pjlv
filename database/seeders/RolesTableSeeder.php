<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'role_name' => 'Super Administrator', 'role_description' => 'Super Administrator', 'role_status' => 1],
            ['id' => 2, 'role_name' => 'Human Resource Manager', 'role_description' => 'Human Resource Manager', 'role_status' => 1],
            ['id' => 3, 'role_name' => 'Chief Executive Officer', 'role_description' => 'Chief Executive Officer', 'role_status' => 1],
            ['id' => 4, 'role_name' => 'Operations Manager', 'role_description' => 'Operations Manager', 'role_status' => 1],
            ['id' => 5, 'role_name' => 'Head of Department', 'role_description' => 'Head of Department', 'role_status' => 1],
            ['id' => 6, 'role_name' => 'Normal', 'role_description' => 'Regular Employee', 'role_status' => 1],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                array_merge($role, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
