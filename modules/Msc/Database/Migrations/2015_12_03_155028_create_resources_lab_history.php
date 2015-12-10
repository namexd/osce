<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesLabHistory extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources_lab_history', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('resources_lab_id');
            $table->dateTime('begin_datetime')->nullable();;
            $table->dateTime('end_datetime')->nullable();;
            $table->integer('group_id')->nullable();;
            $table->integer('teacher_uid')->default(0);
            $table->integer('opertion_uid');
            $table->integer('resources_lab_device_id')->default(0);
            $table->tinyInteger('result_poweroff')->default(1);
            $table->tinyInteger('result_init')->default(1);
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
        Schema::drop('resources_lab_history');
    }

}
