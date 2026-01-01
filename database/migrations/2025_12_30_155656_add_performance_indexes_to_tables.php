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
        Schema::table('staff', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('staff_number');
        });

        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->index('start_date');
            $table->index('end_date');
        });

        Schema::table('leave_actions', function (Blueprint $table) {
            $table->index('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['staff_number']);
        });

        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
        });

        Schema::table('leave_actions', function (Blueprint $table) {
            $table->dropIndex(['status_id']);
        });
    }
};
