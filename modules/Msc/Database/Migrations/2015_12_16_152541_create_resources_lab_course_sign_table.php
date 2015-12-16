<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesLabCourseSignTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources_lab_course_sign', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('resources_lab_plan_id');
            $table->tinyInteger('type')->default(1);  //签到类型  1=签到，2=签出
            $table->unsignedInteger('user_id');
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
        Schema::drop('resources_lab_course_sign');
    }

}
