<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDisciplinePlanTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('discipline_plan', function(Blueprint $table)
		{
			$table->foreign('discipline_id','discipline_plan_discipline_id_foreign')->references('id')->on('discipline')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('studyplan_id','discipline_plan_studyplan_id_foreign')->references('id')->on('studyplan')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('discipline_plan', function(Blueprint $table)
		{
			$table->dropForeign('discipline_plan_discipline_id_foreign');
			$table->dropForeign('discipline_plan_studyplan_id_foreign');
		});
	}

}
