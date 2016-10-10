<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGivenAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('given_answer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_result_id')->unsigned();
            $table->integer('question_id')->unsigned()->nullable()->default(NULL);
            $table->text('answer');
            $table->tinyInteger('right_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('given_answer');
    }
}