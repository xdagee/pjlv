<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->date('start_date')->nullable($value=false);
            $table->date('end_date')->nullable($value=false);
            $table->tinyInteger('leave_days')->nullable($value=false)->default($value=0);
            $table->dateTime('requested_date')->nullable($value=false);
            $table->unsignedInteger('leave_type_id')->nullable($value = false);
            $table->unsignedInteger('staff_id')->nullable($value = false);



            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('staff_leaves');
    }
}
