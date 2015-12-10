<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 17:30:55
// ------------------------------------------------------------

class CreateResourcesClassroomPlanAlterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_classroom_plan_alter', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->unsignedInteger('original_plan_id');
			$table->unsignedInteger('new_plan_id');
			$table->string('description', 255)->nullable();
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
		Schema::drop('resources_classroom_plan_alter');
	}
}