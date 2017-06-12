<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->integer('discipline_id')->unsigned()->nullable()->default(NULL);

            $table->foreign('discipline_id')->references('id')->on('discipline')->onDelete('cascade');
        });

        Schema::table('question', function (Blueprint $table) {
            $table->foreign('theme_id')->references('id')->on('theme')->onDelete('restrict');
        });

        Schema::table('section', function (Blueprint $table) {
            $table->foreign('theme_id')->references('id')->on('theme')->onDelete('cascade');
        });

        Schema::table('test_theme', function (Blueprint $table) {
            $table->foreign('theme_id')->references('id')->on('theme')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('theme', function (Blueprint $table) {
            $table->dropForeign(['discipline_id']);
        });


        Schema::table('question', function (Blueprint $table) {
            $table->dropForeign(['theme_id']);
        });

        Schema::table('section', function (Blueprint $table) {
            $table->dropForeign(['theme_id']);
        });

        Schema::table('test_theme', function (Blueprint $table) {
            $table->dropForeign(['theme_id']);
        });

        Schema::drop('theme');
    }
}