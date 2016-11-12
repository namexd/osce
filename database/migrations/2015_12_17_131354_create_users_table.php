<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:54
// ------------------------------------------------------------

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('username', 16);
			$table->string('name', 50);
			$table->string('mobile', 11)->unique();
			$table->string('password', 255);
			$table->string('nickname', 32);
			$table->boolean('gender');
			$table->string('qq', 32);
			$table->string('openid', 64);
			$table->string('weixinnickname', 64);
			$table->string('country', 32);
			$table->string('province', 32);
			$table->string('city', 32);
			$table->string('adress', 255);
			$table->string('avatar', 255);
			$table->string('email', 50);
			$table->dateTime('lastlogindate')->nullable();
			$table->boolean('idcard_type')->default("1");
			$table->string('idcard', 50);
			$table->boolean('status')->default("1");
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
		Schema::drop('users');
	}
}