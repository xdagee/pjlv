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
        // 1. Drop the FK constraint on users table that forces user_id to equal staff_id
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['id']);
            });
        }

        // 2. Create Admins Table
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            // User ID in users table is `unsignedInteger` (from old migration), so we must match it.
            $table->unsignedInteger('user_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Move Super Admin (Role ID 1) logic
        $superAdminStaff = DB::table('staff')->where('role_id', 1)->first();

        // If no staff with role 1, maybe they are role 0 or something? 
        // Logic assumed role_id 1 is Super Admin.

        if ($superAdminStaff) {
            $user = DB::table('users')->where('id', $superAdminStaff->id)->first();

            DB::table('admins')->insert([
                'user_id' => $superAdminStaff->id, // Preserves the User ID 1 linkage
                'name' => $superAdminStaff->firstname . ' ' . $superAdminStaff->lastname,
                'email' => $user ? $user->email : 'admin@admin.com',
                'phone' => $superAdminStaff->mobile_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Now safely delete the staff record since the FK logic in users table is gone

            if (DB::getDriverName() !== 'sqlite') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }
            DB::table('staff')->where('id', $superAdminStaff->id)->delete();
            if (DB::getDriverName() !== 'sqlite') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');

        // Restore FK?
        // Schema::table('users', function (Blueprint $table) {
        //     $table->foreign('id')->references('id')->on('staff')...
        // });
        // But we deleted the staff record, so adding FK back would fail unless we restore data.
    }
};
