<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourcesClassroomPlanTeacher extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources_classroom_plan_teacher', function(Blueprint $table)
        {
            $table->renameColumn('resources_classroom_plan_id','resources_lab_plan_id')->change();
        });

        Schema::rename('resources_classroom_plan_teacher','resources_lab_plan_teacher');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//
    }

}
