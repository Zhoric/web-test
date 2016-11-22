<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInputParametersFieldToParamSetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('params_set', function (Blueprint $table) {
            $table->text('expected_output')->change();
            $table->text('input');
        });
        Schema::drop('input_param');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('params_set', function (Blueprint $table) {
            $table->string('expected_output', 100)->change();
            $table->dropColumn('input');
        });
        Schema::create('input_param', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('params_set_id')->unsigned();
            $table->integer('number');
            $table->string('type', 100);
            $table->string('value', 100);
        });
    }
}