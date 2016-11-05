<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplinePlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discipline_id')->unsigned();
            $table->integer('studyplan_id')->unsigned();
            $table->smallInteger('start_semester')->nullable()->default(NULL);
            $table->smallInteger('semesters_count')->nullable()->default(NULL);
            $table->smallInteger('hours')->nullable()->default(NULL);
            $table->tinyInteger('has_project')->nullable()->default(NULL);
            $table->tinyInteger('has_exam')->nullable()->default(NULL);

            $table->foreign('discipline_id')->references('id')->on('discipline')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discipline_plan', function (Blueprint $table) {
            $table->dropForeign(['discipline_id']);
        });

        Schema::drop('discipline_plan');
    }
}