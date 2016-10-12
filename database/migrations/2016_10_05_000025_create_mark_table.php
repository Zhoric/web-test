<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mark', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mark_type_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->smallInteger('value')->nullable()->default(NULL);

            $table->foreign('mark_type_id')->references('id')->on('mark_type')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mark', function (Blueprint $table) {
            $table->dropForeign(['mark_type_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::drop('mark');
    }
}