<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesLabPlanTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_lab_plan', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->unsignedInteger('resources_lab_calendar_id');
			$table->unsignedInteger('resources_lab_course_id');
			$table->unsignedInteger('course_id');
			$table->date('currentdate');
			$table->time('begintime');
			$table->time('endtime');
			$table->boolean('type')->default("1");
			$table->boolean('status');
			$table->unsignedInteger('apply_person_total');
			$table->unsignedInteger('resorces_lab_person_total');
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
		Schema::drop('resources_lab_plan');
	}
}