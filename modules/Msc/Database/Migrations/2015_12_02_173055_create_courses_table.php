<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//
// Auto-generated Migration Created: 2015-12-02 17:30:55
// ------------------------------------------------------------

class CreateCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	*/
	public function up()
	{
		Schema::create('courses', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50);
			$table->string('code', 50);
			$table->string('detail', 255);
			$table->unsignedInteger('max_persons');
			$table->unsignedInteger('time_length');
			$table->timestamps();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	*/
	public function down()
	{
		Schema::drop('courses');
	}
}