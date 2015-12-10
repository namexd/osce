<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourcesClassroom extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources_classroom', function(Blueprint $table)
        {
            $table->time('begintime')->change();
            $table->time('endtime')->change();
            $table->tinyInteger('opened');
        });

        Schema::rename('resources_classroom','resources_lab');
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
