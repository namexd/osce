<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesOpenlabVideoTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources_openlab_video', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('resources_openlab_id');
            $table->unsignedInteger('vcr_id');
            $table->dateTime('begin_datetime');
            $table->dateTime('end_datetime');
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
        Schema::drop('resources_openlab_video');
    }

}
