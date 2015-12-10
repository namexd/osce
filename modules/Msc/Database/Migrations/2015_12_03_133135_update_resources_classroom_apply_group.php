<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourcesClassroomApplyGroup extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources_classroom_apply_group', function(Blueprint $table)
        {
            $table->renameColumn('resources_classroom_apply_id','resources_lab_apply_id')->change();
        });

        Schema::rename('resources_classroom_apply_group','resources_lab_apply_group');
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
