<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesDeviceCateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_device_cate', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->unsignedInteger('pid');
			$table->string('name', 50);
			$table->unsignedInteger('manager_id');
			$table->string('manager_name', 50);
			$table->string('manager_mobile', 255);
			$table->string('location', 255);
			$table->string('detail', 255);
			$table->dateTime('created_at')->nullable()->unique();
			$table->dateTime('updated_at')->nullable()->unique();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('resources_device_cate');
	}
}