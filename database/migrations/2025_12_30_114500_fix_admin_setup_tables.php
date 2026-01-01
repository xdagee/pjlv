<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            if (!Schema::hasColumn('holidays', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('holidays', 'created_at')) {
                $table->timestamps();
            }
        });

        Schema::table('leave_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_levels', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn(['name', 'created_at', 'updated_at']);
        });

        Schema::table('leave_levels', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
