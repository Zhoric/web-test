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
                        'type' => QuestionType::ClosedOneAnswer,
                        'text' => 'Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса. Тут текст самого вопроса.Тут текст самого вопроса?',
                        'complexity' => QuestionComplexity::Medium,
                        'time' => 30,
                        'theme_id' => $themeId
                    ));

                $currentQuestionId = ($themeId - 1) * 3 + $i;

                for ($k = 1; $k <= 5; $k++){
                    DB::table('answer')->
                        insert(array(
                            'text' => 'Текст варианта ответа.Текст варианта ответа.Текст варианта ответа.'.$k,
                            'question_id' => $currentQuestionId,
                            'is_right' => ($k == 2) ? true : $faker->boolean(40)
                    ));
                }
            }
        }


        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedManyAnswers,
            'text' => 'Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса. Тут текст самого вопроса.Тут текст самого вопроса?',
            'complexity' => QuestionComplexity::High,
            'time' => 135,
            'theme_id' => 3
        ));

        for ($k = 1; $k <= 6; $k++){
            DB::table('answer')->
            insert(array(
                'text' => 'Текст варианта ответа'.$k,
                'question_id' => 16,
                'is_right' => ($k == 2) ? true : $faker->boolean(40)
            ));
        }

        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedManyAnswers,
            'text' => 'Тут текст самого вопроса. Тут текст самого вопроса. Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса.Тут текст самого вопроса. Тут текст самого вопроса.Тут текст самого вопроса?',
            'complexity' => QuestionComplexity::Low,
            'time' => 200,
            'theme_id' => 3
        ));

        for ($k = 1; $k <= 3; $k++){
            DB::table('answer')->
            insert(array(
                'text' => 'Текст варианта ответа'.$k,
                'question_id' => 17,
                'is_right' => ($k == 3) ? true : $faker->boolean(40)
            ));
        }

        DB::table('question')->
        insert(array(
            'type' => QuestionType::OpenOneString,
            'text' => 'Тут текст однострочного открытого вопроса. Правильные ответы: "Вариант", "Варианты", "Вариант с пробелом", "Вариант с числами123"',
            'complexity' => QuestionComplexity::High,
            'time' => 85,
            'theme_id' => 3
        ));

        DB::table('answer')->
        insert(array(
            'text' => 'Вариант',
            'question_id' => 18,
            'is_right' => ($k == 2) ? true : $faker->boolean(40)
        ));


        DB::table('answer')->
        insert(array(
            'text' => 'Варианты',
            'question_id' => 18,
            'is_right' => ($k == 2) ? true : $faker->boolean(40)
        ));

        DB::table('answer')->
        insert(array(
            'text' => 'Вариант с пробелом',
            'question_id' => 18,
            'is_right' => ($k == 2) ? true : $faker->boolean(40)
        ));

        DB::table('answer')->
        insert(array(
            'text' => 'Вариант с числами123',
            'question_id' => 18,
            'is_right' => ($k == 2) ? true : $faker->boolean(40)
        ));


    }
}