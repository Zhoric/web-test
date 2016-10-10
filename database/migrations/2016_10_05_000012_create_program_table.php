<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id')->unsigned()->nullable()->default(NULL);
            $table->text('template');
            $table->tinyInteger('lang');
        });

        Schema::table('params_set', function (Blueprint $table) {
            $table->foreign('program_id')->references('id')->on('program')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('params_set', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
        });

        Schema::drop('program');
    }
}