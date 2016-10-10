<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('studyplan_id')->unsigned()->nullable()->default(NULL);
            $table->string('prefix', 50)->nullable()->default(NULL);
            $table->smallInteger('course')->nullable()->default(NULL);
            $table->tinyInteger('number');
            $table->tinyInteger('is_fulltime');
            $table->string('name', 100)->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('group');
    }
}