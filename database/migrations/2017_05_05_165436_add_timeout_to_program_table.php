<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeoutToProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('program', function($table)
        {
            $table->integer('time_limit')->unsigned()->default(0);
            $table->integer('memory_limit')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program', function($table)
        {
            $table->dropColumn('time_limit');
            $table->dropColumn('memory_limit');
        });
    }
}
