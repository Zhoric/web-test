<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mediable', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('media_id')->unsigned();
            $table->integer('discipline_id')->unsigned();
            $table->integer('theme_id')->unsigned()->nullable()->default(NULL);
            $table->text('start')->nullable()->default(NULL);
            $table->text('stop')->nullable()->default(NULL);

            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mediable', function (Blueprint $table) {
            $table->dropForeign(['media_id']);
        });

        Schema::drop('mediable');
    }
}
