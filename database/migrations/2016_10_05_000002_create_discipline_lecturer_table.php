<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplineLecturerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_lecturer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lecturer_id')->unsigned();
            $table->integer('discipline_id')->unsigned();

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
        Schema::table('discipline_lecturer', function (Blueprint $table) {
            $table->dropForeign(['discipline_id']);
        });

        Schema::drop('discipline_lecturer');
    }
}