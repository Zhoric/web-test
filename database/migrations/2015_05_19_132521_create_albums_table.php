<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name_ru');
            $table->string('name_en');
            $table->string('description_ru');
            $table->string('description_en');
            $table->string('thumbnail_file_name');
            $table->integer('thumbnail_file_size');
            $table->string('thumbnail_content_type');
            $table->timestamp('thumbnail_updated_at');
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
        Schema::drop('albums');
    }

}
