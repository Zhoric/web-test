<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestMarkTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_mark_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned();
            $table->integer('mark_type_id')->unsigned();
            $table->smallInteger('semester')->nullable()->default(NULL);


            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade');
            $table->foreign('mark_type_id')->references('id')->on('mark_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_mark_type', function (Blueprint $table) {
            $table->dropForeign(['test_id']);
            $table->dropForeign(['mark_type_id']);
        });

        Schema::drop('test_mark_type');
    }
}