<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TestTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('test')->delete();

        for ($i = 1; $i <= 7; $i++){
            DB::table('test')->insert(array(
                'subject' => 'Тест по лабораторной №'.$i,
                'discipline_id' => 2,
                'time_total' => 600,
                'attempts' => 3,
                'type' => 1,
                'is_active' => true,
                'is_random' => true
            ));
        }

        DB::table('test_theme')->insert(array(
            'test_id' => 1,
            'theme_id' => 6
        ));
        DB::table('test_theme')->insert(array(
            'test_id' => 2,
            'theme_id' => 7
        ));
        DB::table('test_theme')->insert(array(
            'test_id' => 3,
            'theme_id' => 8
        ));
        DB::table('test_theme')->insert(array(
            'test_id' => 3,
            'theme_id' => 9
        ));
        DB::table('test_theme')->insert(array(
            'test_id' => 4,
            'theme_id' => 8
        ));
        DB::table('test_theme')->insert(array(
            'test_id' => 4,
            'theme_id' => 9
        ));
        DB::table('test_theme')->insert(array(
            'test_id' => 4,
            'theme_id' => 10
        ));
        DB::table('test_theme')->insert(array(
            'test_id' => 5,
            'theme_id' => 3
        ));
        DB::table('test_theme')->insert(array(
            'test_id' => 6,
            'theme_id' => 10
        ));
    }
}