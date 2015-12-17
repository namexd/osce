<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:54
// ------------------------------------------------------------

class CreateSysPermissionFunctionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_permission_function', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->unsignedInteger('function_id');
			$table->unsignedInteger('permission_id');
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
		Schema::drop('sys_permission_function');
	}
}