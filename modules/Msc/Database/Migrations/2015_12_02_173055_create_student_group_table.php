<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 17:30:55
// ------------------------------------------------------------

class CreateStudentGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('student_group', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('group_id');
			$table->unsignedInteger('student_id');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('student_group');
	}
}