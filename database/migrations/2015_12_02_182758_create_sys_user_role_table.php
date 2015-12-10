<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 18:27:58
// ------------------------------------------------------------

class CreateSysUserRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_user_role', function(Blueprint $table) {
			$table->increments('role_id')->unsigned();
			$table->increments('user_id')->unsigned();
			$table->dateTime('created_at')->nullable()->default("0000-00-00 00:00:00");
			$table->dateTime('updated_at')->nullable()->default("0000-00-00 00:00:00");
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('sys_user_role');
	}
}