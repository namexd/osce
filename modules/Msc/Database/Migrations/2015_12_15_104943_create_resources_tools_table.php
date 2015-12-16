<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesToolsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_tools', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->boolean('repeat_max');
			$table->string('name', 50);
			$table->unsignedInteger('cate_id');
			$table->unsignedInteger('manager_id');
			$table->string('manager_name', 50);
			$table->string('manager_mobile', 255);
			$table->string('location', 50);
			$table->string('detail', 255);
			$table->unsignedInteger('loan_days');
			$table->unsignedInteger('loaned');
			$table->unsignedInteger('total');
			$table->boolean('status')->default("1");
			$table->dateTime('created_at')->unique();
			$table->dateTime('updated_at')->unique();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('resources_tools');
	}
}