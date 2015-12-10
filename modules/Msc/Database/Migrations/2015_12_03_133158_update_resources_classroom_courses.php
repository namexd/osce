<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourcesClassroomCourses extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources_classroom_courses', function(Blueprint $table)
        {
            $table->renameColumn('resources_classroom_id','resources_lab_id')->change();
        });

        Schema::rename('resources_classroom_courses','resources_lab_courses');
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
