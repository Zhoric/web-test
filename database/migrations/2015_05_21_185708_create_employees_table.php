<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('surname');
            $table->string('patronymic');
            $table->string('degree');
            $table->string('post');
            $table->text('pagetext');
            $table->string('photo_file_name');
            $table->integer('photo_file_size');
            $table->string('photo_content_type');
            $table->timestamp('photo_updated_at');
            $table->boolean('questions_enabled');
            $table->integer('order')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('employees');
    }

}
