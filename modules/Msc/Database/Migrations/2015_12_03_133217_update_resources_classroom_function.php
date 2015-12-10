<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourcesClassroomFunction extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources_classroom_function', function(Blueprint $table)
        {
            $table->renameColumn('resources_classroom_id','resources_lab_id')->change();
        });

        Schema::rename('resources_classroom_function','resources_lab_function');
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
