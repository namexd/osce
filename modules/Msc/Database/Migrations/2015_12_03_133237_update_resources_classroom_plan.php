<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourcesClassroomPlan extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources_classroom_plan', function(Blueprint $table)
        {
            $table->renameColumn('resources_classroom_course_id','resources_lab_course_id')->change();
        });

        Schema::rename('resources_classroom_plan','resources_lab_plan');
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
