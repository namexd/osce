<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesOpenlabHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_openlab_history', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('resources_openlab_apply_id');
			$table->unsignedInteger('resources_lab_id');
			$table->dateTime('begin_datetime');
			$table->dateTime('end_datetime');
			$table->unsignedInteger('group_id')->nullable();
			$table->unsignedInteger('teacher_uid')->nullable();
			$table->boolean('result_poweroff')->default("1");
			$table->boolean('result_init')->default("1");
			$table->dateTime('updated_at')->nullable();
			$table->dateTime('created_at')->nullable();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('resources_openlab_history');
	}
}