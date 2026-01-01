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
        // Note: System Admin (user ID 1) is handled by AdminAccountSeeder.
        // This seeder is for any additional user accounts if needed.

        // Staff users are created when staff records are seeded
        // and they register or have accounts created for them.
    }
}

