<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('employee_jobs', function (Blueprint $table) {
            $table->unsignedInteger('emp_id');
            $table->unsignedInteger('job_id');

            
            $table->primary(['emp_id','job_id']);



        $table->foreign('emp_id')->references('id')->on('employees')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::dropIfExists('employee_jobs');
    }
}
