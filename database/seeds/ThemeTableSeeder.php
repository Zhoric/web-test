<?php
use Illuminate\Database\Seeder;

class ThemeTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('theme')->delete();

        for ($disciplineIndex = 1; $disciplineIndex <= 5; $disciplineIndex++){
            $themesCount = 5;

            for($i = 1; $i <= $themesCount; $i++){
                DB::table('theme')->insert(array(
                    'name' => 'Тема '.$i.' - Длинное название темы, очень длинное. На одну трочку не уместить. Вообще никак.',
                    'discipline_id' => $disciplineIndex
                ));
            }
        }

    }
}