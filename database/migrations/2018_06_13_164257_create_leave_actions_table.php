<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('leave_id')->nullable($value = false);
            $table->unsignedInteger('actionby')->nullable($value = false);
            $table->unsignedInteger('status_id')->nullable($value = false)->default(1);
            $table->dateTime('action_date')->nullable($value=false)->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('action_reason')->nullable($value=true);
            


            $table->foreign('leave_id')->references('id')->on('staff_leaves')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('leave_statuses')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('actionby')->references('id')->on('staff')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('leave_actions');
    }
}
