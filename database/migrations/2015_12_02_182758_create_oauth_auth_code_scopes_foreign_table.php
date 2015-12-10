<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 18:27:58
// ------------------------------------------------------------

class CreateOauthAuthCodeScopesForeignTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
Schema::table('oauth_auth_code_scopes', function($table) {
			$table->foreign('auth_code_id')->references('id')->on('oauth_auth_codes');
			$table->foreign('scope_id')->references('id')->on('oauth_scopes');
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