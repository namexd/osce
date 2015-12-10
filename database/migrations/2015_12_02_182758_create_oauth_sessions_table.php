<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 18:27:58
// ------------------------------------------------------------

class CreateOauthSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('oauth_sessions', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('client_id', 40);
			$table->('owner_type')->default("user");
			$table->string('owner_id', 255);
			$table->string('client_redirect_uri', 255)->nullable();
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
		Schema::drop('oauth_sessions');
	}
}