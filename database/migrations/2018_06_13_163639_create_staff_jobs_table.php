<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('staff_jobs', function (Blueprint $table) {
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('job_id');

            
            $table->primary(['staff_id','job_id']);



        $table->foreign('staff_id')->references('id')->on('staff')->onDelete('restrict')->onUpdate('cascade');
        $table->foreign('job_id')->references('id')->on('jobs')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::dropIfExists('staff_jobs');
    }
}
