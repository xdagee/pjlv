<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename existing roles
        DB::table('roles')->where('id', 1)->update(['role_name' => 'Super Admin', 'role_description' => 'System Owner and Super Administrator']);
        DB::table('roles')->where('id', 3)->update(['role_name' => 'CEO', 'role_description' => 'Chief Executive Officer']);
        DB::table('roles')->where('id', 4)->update(['role_name' => 'OPS', 'role_description' => 'Operations Manager/Director']);

        // 2. Add HOD Role
        // Check if exists first to avoid duplicates if re-run
        if (!DB::table('roles')->where('id', 5)->exists()) {
            DB::table('roles')->insert([
                'id' => 5,
                'role_name' => 'Head of Department',
                'role_description' => 'Department Supervisor',
                'role_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert names
        DB::table('roles')->where('id', 1)->update(['role_name' => 'Admin']);
        DB::table('roles')->where('id', 3)->update(['role_name' => 'DG']);
        DB::table('roles')->where('id', 4)->update(['role_name' => 'Director']);

        // Remove HOD
        DB::table('roles')->where('id', 6)->delete();
    }
};
