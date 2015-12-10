<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 17:30:55
// ------------------------------------------------------------

class CreateResourcesClassroomPlanGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_classroom_plan_group', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('resources_classroom_plan_id');
			$table->unsignedInteger('student_class_id');
			$table->unsignedInteger('student_group_id');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('resources_classroom_plan_group');
	}
}