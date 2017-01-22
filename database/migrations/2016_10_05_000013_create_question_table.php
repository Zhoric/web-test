<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('theme_id')->unsigned()->nullable()->default(NULL);
            $table->smallInteger('type');
            $table->text('text')->nullable()->default(NULL);
            $table->string('image', 200)->nullable()->default(NULL);
            $table->smallInteger('complexity')->nullable()->default(NULL);
            $table->smallInteger('time')->nullable()->default(NULL);
        });

        Schema::table('answer', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('question')->onDelete('cascade');
        });

        Schema::table('given_answer', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('question')->onDelete('set null');
        });

        Schema::table('program', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('question')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('answer', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });

        Schema::table('given_answer', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });

        Schema::table('program', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });

        Schema::drop('question');
    }
}