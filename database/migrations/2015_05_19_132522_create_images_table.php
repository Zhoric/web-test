<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('album_id')->unsigned();
            $table->string('name_ru');
            $table->string('name_en');
            $table->integer('order');
            $table->string('image_file_name');
            $table->integer('image_file_size');
            $table->string('image_content_type');
            $table->timestamp('image_updated_at');
            $table->timestamps();

            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('images');
    }

}
