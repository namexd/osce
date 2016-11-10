<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:53
// ------------------------------------------------------------

class CreateSysConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_config', function(Blueprint $table) {
			$table->increments('id');
			$table->string('moduleid', 255);
			$table->string('name', 50);
			$table->string('cate', 255);
			$table->string('type', 255);
			$table->text('value');
			$table->string('description', 255);
			$table->dateTime('created_at');
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
		Schema::drop('sys_config');
	}
}