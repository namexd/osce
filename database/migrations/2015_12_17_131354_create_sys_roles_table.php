<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:54
// ------------------------------------------------------------

class CreateSysRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_roles', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('name', 255);
			$table->string('slug', 255)->unique();
			$table->text('description')->nullable();
			$table->timestamp('created_at')->nullable()->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->nullable()->default("0000-00-00 00:00:00");
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('sys_roles');
	}
}