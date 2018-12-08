<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStudentAttendanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('student_attendance', function(Blueprint $table)
		{
			$table->foreign('discipline_group_id', 'student_attendance_discipline_group_id_foreign')->references('id')->on('discipline_group')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('student_id', 'student_attendance_student_id_foreign')->references('id')->on('user')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('student_attendance', function(Blueprint $table)
		{
			$table->dropForeign('student_attendance_discipline_group_id_foreign');
			$table->dropForeign('student_attendance_student_id_foreign');
		});
	}

}
