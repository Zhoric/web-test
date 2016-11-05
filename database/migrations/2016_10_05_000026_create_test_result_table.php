<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_result', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('mark_type_id')->unsigned()->nullable()->default(NULL);
            $table->smallInteger('attempt')->nullable()->default(NULL);
            $table->smallInteger('mark')->nullable()->default(NULL);

            $table->dateTime('date_time');

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade');
            $table->foreign('mark_type_id')->references('id')->on('mark_type')->onDelete('cascade');

        });

        Schema::table('given_answer', function (Blueprint $table) {
            $table->foreign('test_result_id')->references('id')->on('test_result')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_result', function (Blueprint $table) {
            $table->dropForeign(['test_id']);
            $table->dropForeign(['mark_type_id']);
            $table->dropForeign(['user_id']);
        });


        Schema::table('given_answer', function (Blueprint $table) {
            $table->dropForeign(['test_result_id']);
        });

        Schema::drop('test_result');
    }
}