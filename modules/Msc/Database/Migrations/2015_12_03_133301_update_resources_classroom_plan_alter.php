<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourcesClassroomPlanAlter extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::rename('resources_classroom_plan_alter','resources_lab_plan_alter');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function(Blueprint $table)
        {

        });
    }

}
