<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:53
// ------------------------------------------------------------

class CreateSysFunctionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_functions', function(Blueprint $table) {
			$table->increments('id');
			$table->string('moduleid', 50);
			$table->string('name', 50);
			$table->string('code', 50);
			$table->string('url', 255)->nullable();
			$table->unsignedInteger('pid');
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
		Schema::drop('sys_functions');
	}
}