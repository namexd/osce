<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesOpenlabCalendarTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_openlab_calendar', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('resources_lab_id');
			$table->string('week', 255)->nullable();
			$table->time('begintime')->nullable();
			$table->time('endtime')->nullable();
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
		Schema::drop('resources_openlab_calendar');
	}
}