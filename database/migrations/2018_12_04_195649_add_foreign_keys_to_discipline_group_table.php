<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDisciplineGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('discipline_group', function(Blueprint $table)
		{
			$table->foreign('discipline_id')->references('id')->on('discipline')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('group_id')->references('id')->on('group')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('studyplan_id')->references('id')->on('studyplan')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('discipline_group', function(Blueprint $table)
		{
			$table->dropForeign('discipline_group_discipline_id_foreign');
			$table->dropForeign('discipline_group_group_id_foreign');
			$table->dropForeign('discipline_group_studyplan_id_foreign');
		});
	}

}
