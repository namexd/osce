<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourcesClassroomPlanGroup extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources_classroom_plan_group', function(Blueprint $table)
        {
            $table->renameColumn('resources_classroom_plan_id','resources_lab_plan_id')->change();
        });

        Schema::rename('resources_classroom_plan_group','resources_lab_plan_group');
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
