<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mark_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discipline_plan_id')->unsigned()->nullable();
            $table->string('name', 255)->nullable()->default(NULL);
            $table->smallInteger('number')->nullable()->default(NULL);

            //$table->foreign('discipline_plan_id')->references('id')->on('discipline_plan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('mark_type', function (Blueprint $table) {
            $table->dropForeign(['discipline_plan_id']);
        });

        Schema::drop('mark_type');
    }
}