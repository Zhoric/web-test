<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplineGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_group', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('group_id')->unsigned()->nullable()->index('discipline_group_group_id_foreign');
			$table->integer('discipline_id')->unsigned()->nullable()->index('discipline_group_discipline_id_foreign');
			$table->integer('studyplan_id')->unsigned()->nullable()->index('discipline_group_studyplan_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('discipline_group');
	}

}
