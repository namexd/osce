<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-17 13:13:53
// ------------------------------------------------------------

class CreateSysFailedJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('sys_failed_jobs', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->text('connection');
			$table->text('queue');
			$table->longText('payload');
			$table->timestamp('failed_at')->nullable()->default("0000-00-00 00:00:00");
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('sys_failed_jobs');
	}
}