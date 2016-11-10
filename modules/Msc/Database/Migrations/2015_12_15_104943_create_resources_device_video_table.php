<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesDeviceVideoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_device_video', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('resources_device_id');
			$table->unsignedInteger('vcr_id');
			$table->dateTime('begin_datetime');
			$table->dateTime('end_datetime');
			$table->dateTime('created_at')->nullable();
			$table->dateTime('updated_at')->nullable();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('resources_device_video');
	}
}