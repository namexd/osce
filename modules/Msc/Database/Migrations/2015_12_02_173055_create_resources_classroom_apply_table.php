<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 17:30:55
// ------------------------------------------------------------

class CreateResourcesClassroomApplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_classroom_apply', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('apply_uid');
			$table->unsignedInteger('resources_classroom_id');
			$table->dateTime('original_end_datetime');
			$table->dateTime('original_begin_datetime');
			$table->dateTime('begin_datetime')->nullable();
			$table->dateTime('end_datetime')->nullable();
			$table->string('detail', 255);
			$table->boolean('status');
			$table->string('reject', 255);
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
		Schema::drop('resources_classroom_apply');
	}
}