<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration to create leave_audits table for comprehensive audit logging.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_audits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('leave_id');
            $table->unsignedInteger('actor_id');
            $table->string('action', 50);
            $table->unsignedTinyInteger('old_status_id')->nullable();
            $table->unsignedTinyInteger('new_status_id')->nullable();
            $table->text('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('leave_id');
            $table->index('actor_id');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_audits');
    }
};
