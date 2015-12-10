<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 18:27:58
// ------------------------------------------------------------

class CreateOauthRefreshTokensForeignTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
Schema::table('oauth_refresh_tokens', function($table) {
			$table->foreign('access_token_id')->references('id')->on('oauth_access_tokens');
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