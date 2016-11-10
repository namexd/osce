<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesLabApplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_lab_apply', function(Blueprint $table) {
			$table->increments('id');
			$table->boolean('apply_user_type');
			$table->boolean('apply_type');
			$table->unsignedInteger('apply_uid');
			$table->unsignedInteger('resources_lab_id');
			$table->dateTime('original_end_datetime');
			$table->dateTime('original_begin_datetime');
			$table->dateTime('begin_datetime')->nullable();
			$table->dateTime('end_datetime')->nullable();
			$table->string('detail', 255);
			$table->boolean('status');
			$table->string('reject', 255);
			$table->unsignedInteger('opeation_uid');
			$table->unsignedInteger('resources_lab_device_id');
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
		Schema::drop('resources_lab_apply');
	}
}