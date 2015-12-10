<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 18:27:58
// ------------------------------------------------------------

class CreateOauthClientGrantsForeignTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
Schema::table('oauth_client_grants', function($table) {
			$table->foreign('client_id')->references('id')->on('oauth_clients');
			$table->foreign('grant_id')->references('id')->on('oauth_grants');
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