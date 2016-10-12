<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discipline_id')->unsigned();
            $table->string('subject', 200)->nullable()->default(NULL);
            $table->smallInteger('time_total')->nullable()->default(NULL);
            $table->smallInteger('attempts')->nullable()->default(NULL);
            $table->smallInteger('order_number')->nullable()->default(NULL);
            $table->smallInteger('fulltime_start')->nullable()->default(NULL);
            $table->smallInteger('extramural_start')->nullable()->default(NULL);

            $table->foreign('discipline_id')->references('id')->on('discipline')->onDelete('cascade');
        });

        Schema::table('extra_attempt', function (Blueprint $table) {
            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test', function (Blueprint $table) {
            $table->dropForeign(['discipline_id']);
        });


        Schema::table('extra_attempt', function (Blueprint $table) {
            $table->dropForeign(['test_id']);
        });

        Schema::drop('test');
    }
}