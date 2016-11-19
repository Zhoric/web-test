<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisciplineSectionForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section', function ($table) {
            $table->integer('theme_id')->unsigned()->nullable()->default(NULL)->change();
            $table->integer('discipline_id')->unsigned()->nullable()->default(NULL);
            $table->foreign('discipline_id')->references('id')->on('discipline')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section', function ($table) {
            $table->integer('theme_id')->default(NULL)->change();
            $table->dropForeign(['discipline_id']);
            $table->dropColumn('discipline_id');
        });
    }
}