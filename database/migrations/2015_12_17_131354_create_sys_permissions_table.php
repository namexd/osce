<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:54
// ------------------------------------------------------------

class CreateSysPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_permissions', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('moduleid', 50);
			$table->string('type', 50);
			$table->unsignedInteger('itemid');
			$table->string('name', 50);
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
		Schema::drop('sys_permissions');
	}
}