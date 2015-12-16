<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesDeviceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_device', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('resources_lab_id');
			$table->string('name', 50);
			$table->string('code', 255);
			$table->unsignedInteger('resources_device_cate_id');
			$table->unsignedInteger('max_use_time');
			$table->text('warning')->nullable();
			$table->text('detail')->nullable();
			$table->boolean('status')->default("1");
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
		Schema::drop('resources_device');
	}
}