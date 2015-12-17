<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:54
// ------------------------------------------------------------

class CreateUsersPasswordResetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('users_password_resets', function(Blueprint $table) {
			$table->increments('id');
			$table->string('mobile', 11)->nullable();
			$table->string('email', 50)->nullable();
			$table->string('token', 255);
			$table->string('wx_openid', 255)->nullable();
			$table->dateTime('created_at')->nullable();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('users_password_resets');
	}
}