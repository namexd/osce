<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesLabVcrTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources_lab_vcr', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('resources_lab_id');
            $table->unsignedInteger('vcr_id');
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
        Schema::drop('resources_lab_vcr');
    }

}
