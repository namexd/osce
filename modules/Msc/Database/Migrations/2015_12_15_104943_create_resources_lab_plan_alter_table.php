<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesLabPlanAlterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_lab_plan_alter', function(Blueprint $table) {
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
		Schema::drop('resources_lab_plan_alter');
	}
}