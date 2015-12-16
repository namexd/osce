<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesToolsItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_tools_items', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('resources_tool_id');
			$table->string('code', 50);
			$table->boolean('status')->default("1");
			$table->string('reject_detail', 255);
			$table->dateTime('reject_date');
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
		Schema::drop('resources_tools_items');
	}
}