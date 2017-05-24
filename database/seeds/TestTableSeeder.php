<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TestTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('test')->delete();

        DB::table('test')->insert(array(
            'subject' => 'Основы программирования на языке C++',
            'discipline_id' => 1,
            'time_total' => 600,
            'attempts' => 5,
            'type' => 1,
            'is_active' => true,
            'is_random' => true
        ));

        DB::table('test')->insert(array(
            'subject' => 'Тест по основам веб-технологий №1',
            'discipline_id' => 2,
            'time_total' => 500,
            'attempts' => 5,
            'type' => 1,
            'is_active' => true,
            'is_random' => true
        ));

        DB::table('test_theme')->insert(array(
            'test_id' => 1,
            'theme_id' => 1
        ));

        DB::table('test_theme')->insert(array(
            'test_id' => 2,
            'theme_id' => 2
        ));
    }
}