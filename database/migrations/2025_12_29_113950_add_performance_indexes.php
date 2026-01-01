<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration to add performance indexes on frequently queried columns.
 * This improves query performance for leave lookups, approvals, and reporting.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to staff_leaves table for common queries
        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->index(['staff_id', 'start_date'], 'staff_leaves_staff_date_idx');
            $table->index('leave_type_id', 'staff_leaves_type_idx');
            $table->index(['start_date', 'end_date'], 'staff_leaves_date_range_idx');
        });

        // Add indexes to leave_actions table for status lookups
        Schema::table('leave_actions', function (Blueprint $table) {
            $table->index(['leave_id', 'status_id'], 'leave_actions_leave_status_idx');
            $table->index('actionby', 'leave_actions_actionby_idx');
        });

        // Add timestamps to leave_actions for audit trail (if not exists)
        if (!Schema::hasColumn('leave_actions', 'created_at')) {
            Schema::table('leave_actions', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Add index to staff table for active staff lookups
        Schema::table('staff', function (Blueprint $table) {
            $table->index('is_active', 'staff_active_idx');
            $table->index('role_id', 'staff_role_idx');
            $table->index('supervisor_id', 'staff_supervisor_idx');
        });

        // Add index to holidays table for date lookups
        Schema::table('holidays', function (Blueprint $table) {
            $table->index('date', 'holidays_date_idx');
        });

        // Add index to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'read_at'], 'notifications_user_read_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->dropIndex('staff_leaves_staff_date_idx');
            $table->dropIndex('staff_leaves_type_idx');
            $table->dropIndex('staff_leaves_date_range_idx');
        });

        Schema::table('leave_actions', function (Blueprint $table) {
            $table->dropIndex('leave_actions_leave_status_idx');
            $table->dropIndex('leave_actions_actionby_idx');
            if (Schema::hasColumn('leave_actions', 'created_at')) {
                $table->dropTimestamps();
            }
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex('staff_active_idx');
            $table->dropIndex('staff_role_idx');
            $table->dropIndex('staff_supervisor_idx');
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->dropIndex('holidays_date_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_idx');
        });
    }
};
