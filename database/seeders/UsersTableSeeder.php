<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        DB::table('users')->insert([
            'id' => 1,
            'email' => 'admin@admin.com',
            'password' => Hash::make('adminpass'),
            'remember_token' => Str::random(10)
        ]);

        // HR Manager
        DB::table('users')->insert([
            'id' => 2,
            'email' => 'hr@company.com',
            'password' => Hash::make('hrpass123'),
            'remember_token' => Str::random(10)
        ]);

        // Normal Employee
        DB::table('users')->insert([
            'id' => 3,
            'email' => 'john.doe@company.com',
            'password' => Hash::make('userpass123'),
            'remember_token' => Str::random(10)
        ]);
    }
}
