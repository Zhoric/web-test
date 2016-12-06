<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TestTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('test')->delete();

        for ($j = 1; $j <= 5; $j++){
            for ($i = 1; $i <= 5; $i++){
                DB::table('test')->insert(array(
                    'subject' => 'Тест по лабораторной №'.$i,
                    'discipline_id' => $j,
                    'time_total' => 600,
                    'attempts' => 5,
                    'type' => 1,
                    'is_active' => true,
                    'is_random' => true
                ));

                $testId = ($j - 1) * 5 + $i;

                DB::table('test_theme')->insert(array(
                    'test_id' => $testId,
                    'theme_id' => $testId
                ));
            }
        }
    }
}