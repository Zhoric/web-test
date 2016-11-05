<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('patronymic');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->boolean('active')->default(0);
            $table->boolean('confirmed')->default(0);
            $table->string('confirmation_code', 64)->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }

}
