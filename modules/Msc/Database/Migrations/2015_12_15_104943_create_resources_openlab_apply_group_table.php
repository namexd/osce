<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesOpenlabApplyGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_openlab_apply_group', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('resources_openlab_apply_id');
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
		Schema::drop('resources_openlab_apply_group');
	}
}