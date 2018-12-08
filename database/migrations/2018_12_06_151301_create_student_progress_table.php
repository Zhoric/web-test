<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentProgressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student_progress', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('discipline_group_id')->nullable()->index('student_progress_discipline_group_id_foreign');
			$table->integer('student_id')->unsigned()->nullable()->index('student_progress_student_id_foreign');
			$table->string('occupation_type', 50)->nullable();
			$table->smallInteger('work_number')->nullable();
			$table->string('visit_status', 20)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('student_progress');
	}

}
