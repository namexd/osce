<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesLabCalendar extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources_lab_calendar', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('resoutces_lab_id');
            $table->tinyInteger('type');
            $table->string('week',32)->nullable();
            $table->time('day_begintime')->nullable();
            $table->time('day_endtime')->nullable();
            $table->string('month')->nullable();
            $table->string('days')->nullable();
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
        Schema::drop('resources_lab_calendar');
    }

}
