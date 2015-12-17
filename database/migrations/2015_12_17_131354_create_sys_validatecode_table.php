<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:54
// ------------------------------------------------------------

class CreateSysValidatecodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_validatecode', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->unsignedInteger('uid');
			$table->string('mobile', 255)->nullable();
			$table->unsignedInteger('expiretime')->unsigned();
			$table->boolean('type')->default("1");
			$table->string('code', 255);
			$table->string('email', 50)->nullable();
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
		Schema::drop('sys_validatecode');
	}
}