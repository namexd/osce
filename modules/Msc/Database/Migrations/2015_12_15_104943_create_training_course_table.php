<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateTrainingCourseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('training_course', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('course_id');
			$table->unsignedInteger('training_id');
			$table->unsignedInteger('resources_lab_id');
			$table->dateTime('begin_dt');
			$table->dateTime('end_dt');
			$table->boolean('validation_pass');
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
		Schema::drop('training_course');
	}
}