<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInputParamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_param', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('params_set_id')->unsigned();
            $table->integer('number');
            $table->string('type', 100);
            $table->string('value', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('input_param');
    }
}