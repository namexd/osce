<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 17:30:55
// ------------------------------------------------------------

class CreateResourcesClassroomTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_classroom', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50);
			$table->string('code', 50);
			$table->string('location', 50);
			$table->dateTime('begintime')->nullable();
			$table->dateTime('endtime')->nullable();
			$table->unsignedInteger('manager_id');
			$table->string('manager_name', 50);
			$table->string('manager_mobile', 255);
			$table->string('detail', 255);
			$table->boolean('status')->default("1");
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
		Schema::drop('resources_classroom');
	}
}