<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 18:27:58
// ------------------------------------------------------------

class CreateOauthAuthCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('oauth_auth_codes', function(Blueprint $table) {
			$table->increments('id', 40);
			$table->unsignedInteger('session_id')->unsigned();
			$table->string('redirect_uri', 255);
			$table->unsignedInteger('expire_time');
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
		Schema::drop('oauth_auth_codes');
	}
}