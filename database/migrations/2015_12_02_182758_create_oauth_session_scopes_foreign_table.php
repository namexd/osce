<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 18:27:58
// ------------------------------------------------------------

class CreateOauthSessionScopesForeignTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
Schema::table('oauth_session_scopes', function($table) {
			$table->foreign('scope_id')->references('id')->on('oauth_scopes');
			$table->foreign('session_id')->references('id')->on('oauth_sessions');
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