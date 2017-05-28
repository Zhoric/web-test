<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MarkTypeTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('mark_type')->delete();

    }
}