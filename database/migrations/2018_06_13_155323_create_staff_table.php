<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('id');
            $table->string('staff_number', 11)->nullable($value = false);
            $table->string('title',5)->nullable($value =false);
            $table->string('firstname',125)->nullable($value = false);
            $table->string('lastname',125)->nullable($value = false);
            $table->string('othername',125)->nullable();
            $table->date('dob')->nullable($value = false);
            $table->string('mobile_number',14)->nullable($value = false);
            $table->boolean('gender')->nullable($value = false);
            $table->string('picture',125)->nullable();
            $table->boolean('is_active')->nullable($value = false);
            $table->date('date_joined')->nullable($value = false);
            $table->unsignedInteger('leave_level_id')->nullable($value = true);
            $table->integer('total_leave_days')->nullable($value = false);
            $table->integer('supervisor_id')->nullable();
            $table->unsignedInteger('role_id')->nullable($value = false);

            
        
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('leave_level_id')->references('id')->on('leave_levels')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();  
        });

        // Schema::table('employees', function($table){
        //     $table->foreign('leave_level_id')->references('id')->on('leave_levels');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('staff');
    }
}
