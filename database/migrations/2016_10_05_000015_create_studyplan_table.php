<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyplanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studyplan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id')->unsigned();
            $table->string('name', 255)->nullable()->default(NULL);
            $table->integer('year')->nullable();


            $table->foreign('profile_id')->references('id')->on('profile')->onDelete('restrict');
        });

        Schema::table('group', function (Blueprint $table) {
            $table->foreign('studyplan_id')->references('id')->on('studyplan')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('studyplan', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
        });


        Schema::table('discipline_plan', function (Blueprint $table) {
            $table->dropForeign(['studyplan_id']);
        });

        Schema::table('group', function (Blueprint $table) {
            $table->dropForeign(['studyplan_id']);
        });

        Schema::drop('studyplan');
    }
}