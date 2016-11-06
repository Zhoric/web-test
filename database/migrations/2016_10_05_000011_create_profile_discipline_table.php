<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_discipline', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discipline_id')->unsigned();
            $table->integer('profile_id')->unsigned();

            $table->foreign('discipline_id')->references('id')->on('discipline')->onDelete('cascade');
            $table->foreign('profile_id')->references('id')->on('profile')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_discipline', function (Blueprint $table) {
            $table->dropForeign(['discipline_id']);
            $table->dropForeign(['profile_id']);
        });

        Schema::drop('profile_discipline');
    }
}