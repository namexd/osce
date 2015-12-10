<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 17:30:55
// ------------------------------------------------------------

class CreateResourcesClassroomPlanTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_classroom_plan', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->unsignedInteger('resources_classroom_course_id');
			$table->unsignedInteger('course_id');
			$table->date('currentdate');
			$table->time('begintime');
			$table->time('endtime');
			$table->boolean('type')->default("1");
			$table->boolean('status');
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
		Schema::drop('resources_classroom_plan');
	}
}