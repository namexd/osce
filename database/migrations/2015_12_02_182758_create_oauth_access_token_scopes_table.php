<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 18:27:58
// ------------------------------------------------------------

class CreateOauthAccessTokenScopesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('oauth_access_token_scopes', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('access_token_id', 40);
			$table->string('scope_id', 40);
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('oauth_access_token_scopes');
	}
}