<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-15 10:49:43
// ------------------------------------------------------------

class CreateStudentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('student', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('name', 50)->nullable();
			$table->string('code', 32);
			$table->string('qq', 32);
			$table->unsignedInteger('class');
			$table->unsignedInteger('grade');
			$table->unsignedInteger('professional');
			$table->boolean('student_type')->unsigned();
			$table->boolean('validated')->unsigned();
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
		Schema::drop('student');
	}
}