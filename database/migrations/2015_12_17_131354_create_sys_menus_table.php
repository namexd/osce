<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:54
// ------------------------------------------------------------

class CreateSysMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_menus', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('moduleid', 50);
			$table->string('name', 50);
			$table->string('url', 255);
			$table->unsignedInteger('pid');
			$table->string('ico', 255)->nullable();
			$table->unsignedInteger('order');
			$table->string('descrition', 255)->nullable();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('sys_menus');
	}
}