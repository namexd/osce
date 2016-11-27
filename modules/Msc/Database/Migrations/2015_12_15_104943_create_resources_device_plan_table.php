<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesDevicePlanTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_device_plan', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->unsignedInteger('resources_device_id');
			$table->date('currentdate');
			$table->time('begintime');
			$table->time('endtime');
			$table->boolean('status');
			$table->unsignedInteger('opertion_uid');
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
		Schema::drop('resources_device_plan');
	}
}