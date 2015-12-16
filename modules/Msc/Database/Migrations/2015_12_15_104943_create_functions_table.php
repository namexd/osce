<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateFunctionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('functions', function(Blueprint $table) {
			$table->increments('id');
			$table->string('type', 50);
			$table->string('name', 50);
			$table->unsignedInteger('time_length');
			$table->unsignedInteger('max_persons');
			$table->string('description', 255)->nullable();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('functions');
	}
}