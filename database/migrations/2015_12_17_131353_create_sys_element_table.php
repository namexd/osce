<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:53
// ------------------------------------------------------------

class CreateSysElementTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_element', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('moduleid', 50);
			$table->string('page', 50)->nullable();
			$table->string('name', 50);
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
		Schema::drop('sys_element');
	}
}