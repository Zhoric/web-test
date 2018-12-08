<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplinePlanTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_plan', function(Blueprint $table)
		{


			$table->integer('id', true);
			$table->integer('discipline_id')->unsigned()->nullable()->index('discipline_plan_discipline_id_foreign');
			$table->integer('studyplan_id')->unsigned()->nullable()->index('discipline_plan_studyplan_id_foreign');
			$table->smallInteger('semester')->nullable();
			$table->boolean('has_exam')->nullable();
			$table->boolean('has_coursework')->nullable();
			$table->boolean('has_course_project')->nullable();
			$table->boolean('has_design_assignment')->nullable();
			$table->boolean('has_essay')->nullable();
			$table->boolean('has_home_test')->nullable();
			$table->boolean('has_audience_test')->nullable();
			$table->smallInteger('hours_all')->nullable();
			$table->smallInteger('hours_lecture')->nullable();
			$table->smallInteger('hours_laboratory')->nullable();
			$table->smallInteger('hours_practical')->nullable();
			$table->smallInteger('hours_solo')->nullable();
			$table->smallInteger('count_lecture')->nullable();
			$table->smallInteger('count_laboratory')->nullable();
			$table->smallInteger('count_practical')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('discipline_plan');
	}

}
