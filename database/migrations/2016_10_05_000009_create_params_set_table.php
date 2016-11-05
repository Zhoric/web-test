<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParamsSetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('params_set', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('program_id')->unsigned();
            $table->string('expected_output', 100);
        });

        Schema::table('input_param', function (Blueprint $table) {
            $table->foreign('params_set_id')->references('id')->on('params_set')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('input_param', function (Blueprint $table) {
            $table->dropForeign(['params_set_id']);
        });

        Schema::drop('params_set');
    }
}