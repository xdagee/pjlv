<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_levels', function (Blueprint $table) {
            $table->increments('id');
            $table-> string('level_name',17)->nullable($value = false)->unique();
            $table->tinyInteger('annual_leave_days')->nullable($value = false);
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('leave_levels');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
