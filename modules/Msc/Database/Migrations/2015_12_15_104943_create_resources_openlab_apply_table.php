<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesOpenlabApplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_openlab_apply', function(Blueprint $table) {
			$table->increments('id');
			$table->boolean('apply_type');
			$table->date('apply_date');
			$table->unsignedInteger('apply_uid');
			$table->unsignedInteger('resources_lab_id');
			$table->unsignedInteger('resources_lab_calendar_id');
			$table->string('detail', 255);
			$table->boolean('status');
			$table->string('reject', 255);
			$table->unsignedInteger('course_id');
			$table->unsignedInteger('opeation_uid');
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
		Schema::drop('resources_openlab_apply');
	}
}