<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyplansTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studyplans', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('discipline_id');
            $table->unsignedInteger('semester');
            $table->unsignedInteger('hours');
            $table->string('degree');
            $table->boolean('has_project');
            $table->boolean('has_exam');
            $table->timestamps();

            $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('studyplans');
    }

}
