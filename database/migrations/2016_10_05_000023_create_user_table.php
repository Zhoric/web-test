<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname', 255)->nullable()->default(NULL);
            $table->string('patronymic', 255)->nullable()->default(NULL);
            $table->string('lastname', 255)->nullable()->default(NULL);
            $table->string('email', 255)->nullable()->default(NULL);
            $table->string('password', 60)->nullable()->default(NULL);
            $table->tinyInteger('active')->nullable()->default(NULL);
            $table->string('remember_token', 100)->nullable()->default(NULL);
        });

        Schema::table('discipline_lecturer', function (Blueprint $table) {
            $table->foreign('lecturer_id')->references('id')->on('user')->onDelete('cascade');
        });

        Schema::table('extra_attempt', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });

        Schema::table('student_group', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('user')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('discipline_lecturer', function (Blueprint $table) {
            $table->dropForeign(['lecturer_id']);
        });

        Schema::table('extra_attempt', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('student_group', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::drop('user');
    }
}