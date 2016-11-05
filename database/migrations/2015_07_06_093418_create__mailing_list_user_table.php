<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailingListUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mailing_list_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mailing_list_id');
            $table->unsignedInteger('user_id');

            $table->foreign('mailing_list_id')->references('id')->on('mailing_lists')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('mailing_list_user');
    }

}
