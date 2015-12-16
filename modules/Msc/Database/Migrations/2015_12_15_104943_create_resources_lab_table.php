<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesLabTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_lab', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50);
			$table->string('code', 50);
			$table->string('location', 50);
			$table->time('begintime')->nullable();
			$table->time('endtime')->nullable();
			$table->boolean('opened');
			$table->unsignedInteger('manager_id');
			$table->string('manager_name', 50);
			$table->string('manager_mobile', 255);
			$table->string('detail', 255);
			$table->boolean('status')->default("1");
			$table->unsignedInteger('person_total');
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
		Schema::drop('resources_lab');
	}
}