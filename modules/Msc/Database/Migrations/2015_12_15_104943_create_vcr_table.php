<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateVcrTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('vcr', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50);
			$table->string('code', 255)->nullable();
			$table->string('ip', 50);
			$table->string('username', 50);
			$table->string('password', 50);
			$table->unsignedInteger('port');
			$table->string('channel', 50);
			$table->string('description', 255);
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
		Schema::drop('vcr');
	}
}