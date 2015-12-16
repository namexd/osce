<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateResourcesToolsBorrowingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('resources_tools_borrowing', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('code', 64)->nullable()->unique();
			$table->unsignedInteger('resources_tool_id');
			$table->unsignedInteger('resources_tool_item_id')->nullable();
			$table->dateTime('begindate')->nullable();
			$table->dateTime('enddate')->nullable();
			$table->dateTime('real_begindate')->nullable();
			$table->dateTime('real_enddate')->nullable();
			$table->unsignedInteger('lender')->nullable();
			$table->unsignedInteger('agent_id')->nullable();
			$table->string('agent_name', 32)->nullable();
			$table->string('detail', 255);
			$table->text('description')->nullable();
			$table->unsignedInteger('loan_operator_id');
			$table->boolean('status')->default("1");
			$table->boolean('loan_validated');
			$table->boolean('apply_validated');
			$table->string('return_detail', 255);
			$table->unsignedInteger('return_operator_id');
			$table->boolean('repeat_number');
			$table->unsignedInteger('pid');
			$table->text('bad_images')->nullable();
			$table->string('bad_description', 255)->nullable();
			$table->dateTime('created_at')->default("0000-00-00 00:00:00");
			$table->dateTime('updated_at')->default("0000-00-00 00:00:00");
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('resources_tools_borrowing');
	}
}