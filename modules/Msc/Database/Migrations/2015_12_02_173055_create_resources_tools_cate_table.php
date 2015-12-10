<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 17:30:55
// ------------------------------------------------------------

class CreateResourcesToolsCateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_tools_cate', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->boolean('repeat_max');
			$table->unsignedInteger('pid');
			$table->string('name', 50);
			$table->unsignedInteger('manager_id');
			$table->string('manager_name', 50);
			$table->string('manager_mobile', 255);
			$table->string('location', 255);
			$table->string('detail', 255);
			$table->unsignedInteger('loan_days');
			$table->dateTime('created_at')->nullable()->unique();
			$table->dateTime('updated_at')->nullable()->unique();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('resources_tools_cate');
	}
}