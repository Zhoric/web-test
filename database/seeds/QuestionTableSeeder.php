<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class QuestionTableSeeder extends Seeder
{

    public function run()
    {
        $faker = Faker::create('ru_RU');

        DB::table('question')->delete();

        for ($themeId = 1; $themeId <= 5; $themeId++){

            for($i = 1; $i <= 3; $i++){
                DB::table('question')->
                    insert(array(
                        'type' => QuestionType::Closed,
                        'text' => 'Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса. Тут текст самого вопроса.Тут текст самого вопроса?',
                        'complexity' => QuestionComplexity::Medium,
                        'time' => 30,
                        'theme_id' => $themeId
                    ));

                $currentQuestionId = ($themeId - 1) * 3 + $i;

                for ($k = 1; $k <= 5; $k++){
                    DB::table('answer')->
                        insert(array(
                            'text' => 'Текст варианта ответа.Текст варианта ответа.Текст варианта ответа.Текст варианта ответа.Текст варианта ответа.',
                            'question_id' => $currentQuestionId,
                            'is_right' => ($k == 2) ? true : $faker->boolean(40)
                    ));
                }
            }
        }



    }
}