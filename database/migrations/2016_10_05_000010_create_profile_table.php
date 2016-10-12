<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id')->unsigned()->nullable()->default(NULL);
            $table->string('code', 50)->nullable()->default(NULL);
            $table->string('name', 100);
            $table->string('fullname', 255)->nullable()->default(NULL);
            $table->smallInteger('semesters')->nullable()->default(NULL);

            $table->foreign('institute_id')->references('id')->on('institute')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile', function (Blueprint $table) {
            $table->dropForeign(['institute_id']);
        });

        Schema::drop('profile');
    }
}