<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class QuestionTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('question')->delete();

        DB::table('question')->
        insert(array(
            'type' => QuestionType::OpenOneString,
            'text' => 'Процесс упорядоченного размещения элементов в массиве называется...',
            'complexity' => QuestionComplexity::Medium,
            'time' => 45,
            'theme_id' => 1
        ));

        DB::table('answer')->
        insert(array(
            'text' => 'сортировка',
            'question_id' => 1,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'сортировать',
            'question_id' => 1,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'sort',
            'question_id' => 1,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'сортировкой',
            'question_id' => 1,
            'is_right' => true
        ));
//------------------------------------------------------------------------------------------

        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedOneAnswer,
            'text' => 'Выберите недостающее слово:
            Указатель - это переменная, которая содержит в качестве своего значения ______ другой переменной.',
            'complexity' => QuestionComplexity::Medium,
            'time' => 30,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'индекс',
            'question_id' => 2,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'ссылка',
            'question_id' => 2,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'адрес',
            'question_id' => 2,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'код',
            'question_id' => 2,
            'is_right' => false
        ));
//-------------------------------------------------------------------------

        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedOneAnswer,
            'text' => 'Что такое идентификаторы в языке Си++ ?',
            'complexity' => QuestionComplexity::Low,
            'time' => 30,
            'theme_id' => 1
        ));

        DB::table('answer')->
        insert(array(
            'text' => 'это последовательность знаков, начинающаяся с буквы или знака подчеркивания',
            'question_id' => 3,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'это последовательность знаков',
            'question_id' => 3,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'это последовательность знаков, начинающаяся с буквы',
            'question_id' => 3,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'это последовательность знаков, начинающаяся со знака подчеркивания',
            'question_id' => 3,
            'is_right' => false
        ));

//------------------------------------------------------------------------

        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedManyAnswers,
            'text' => 'При помощи каких из приведённых символов в языке С++ можно определить комментарий?',
            'complexity' => QuestionComplexity::Medium,
            'time' => 30,
            'theme_id' => 1
        ));

        DB::table('answer')->
        insert(array(
            'text' => ';',
            'question_id' => 4,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => '--',
            'question_id' => 4,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => '//',
            'question_id' => 4,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => '/**/',
            'question_id' => 4,
            'is_right' => true
        ));

//------------------------------------------------------------------------

        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedManyAnswers,
            'text' => 'Фактический адрес в указателях - это:',
            'complexity' => QuestionComplexity::Medium,
            'time' => 35,
            'theme_id' => 1
        ));

        DB::table('answer')->
        insert(array(
            'text' => 'строка',
            'question_id' => 5,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'указатель',
            'question_id' => 5,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'число',
            'question_id' => 5,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'буква',
            'question_id' => 5,
            'is_right' => false
        ));

//------------------------------------------------------------------------

        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedManyAnswers,
            'text' => 'Логическое «не равно» обозначается: ',
            'complexity' => QuestionComplexity::Low,
            'time' => 20,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => '<>',
            'question_id' => 6,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => '||',
            'question_id' => 6,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => '!',
            'question_id' => 6,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => '!=',
            'question_id' => 6,
            'is_right' => true
        ));

//------------------------------------------------------------------------

        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedManyAnswers,
            'text' => 'Тернарное выражение - это: ',
            'complexity' => QuestionComplexity::Medium,
            'time' => 30,
            'theme_id' => 1
        ));

        DB::table('answer')->
        insert(array(
            'text' => 'компактный способ записи оператора WHILE/DO',
            'question_id' => 7,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'компактный способ записи оператора IF/ELSE',
            'question_id' => 7,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'выбор одного из нескольких вариантов',
            'question_id' => 7,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'выражение, описывающее действия логических связывающих операторов на переменные',
            'question_id' => 7,
            'is_right' => false
        ));
//-------------------------------------------------------------------------
        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedOneAnswer,
            'text' => "Писать #include <stdio.h> нужно для:",
            'complexity' => QuestionComplexity::Medium,
            'time' => 40,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'подключения файла, содержащего макроопределения и объявления данных, необходимых для работы функций из стандартной библиотеки ввода-вывода',
            'question_id' => 8,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'позволяет дать в программе макроопределения (или задать макросы)',
            'question_id' => 8,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'переопределения не только константы, но и целых программных конструкций',
            'question_id' => 8,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'замены каждого параметра в строке лексем на соответствующий аргумент макровызова',
            'question_id' => 8,
            'is_right' => false
        ));
//-------------------------------------------------------------------------
        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedOneAnswer,
            'text' => "В языке Си++ тело функции ограничено операторными скобками:",
            'complexity' => QuestionComplexity::Low,
            'time' => 20,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'begin end',
            'question_id' => 9,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'start finish',
            'question_id' => 9,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => '[]',
            'question_id' => 9,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => '{}',
            'question_id' => 9,
            'is_right' => true
        ));
//-------------------------------------------------------------------------
        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedOneAnswer,
            'text' => "Лидирующий нуль в литералах означает:",
            'complexity' => QuestionComplexity::Medium,
            'time' => 30,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'числовой шестнадцатеричный литерал',
            'question_id' => 10,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'вещественный десятичный литерал',
            'question_id' => 10,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'числовой восьмеричный литерал',
            'question_id' => 10,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'целый десятичный литерал',
            'question_id' => 10,
            'is_right' => false
        ));

//-------------------------------------------------------------------------
        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedManyAnswers,
            'text' => "Метки в операторе Switch должны быть:",
            'complexity' => QuestionComplexity::Medium,
            'time' => 25,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'указателями',
            'question_id' => 11,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'переменной',
            'question_id' => 11,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'константой',
            'question_id' => 11,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'типа Char',
            'question_id' => 11,
            'is_right' => false
        ));
//-------------------------------------------------------------------------
        DB::table('question')->
        insert(array(
            'type' => QuestionType::ClosedManyAnswers,
            'text' => "Каков будет результат выполнения операторов:
int i,j,s;
i=j=2;          /* i и j получают значение 2 */
s=(i++)+(++j);",
            'complexity' => QuestionComplexity::High,
            'time' => 50,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'i = 3, j = 2, s = 5',
            'question_id' => 12,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'i = 3, j = 3, s = 6',
            'question_id' => 12,
            'is_right' => false
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'i = 3, j = 3, s = 5',
            'question_id' => 12,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'i = 2, j = 3, s = 5',
            'question_id' => 12,
            'is_right' => false
        ));
//-------------------------------------------------------------------------
        DB::table('question')->
        insert(array(
            'type' => QuestionType::OpenOneString,
            'text' => "Какое расширение имеют файлы исходного кода языка C++?",
            'complexity' => QuestionComplexity::Medium,
            'time' => 30,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => 'cpp',
            'question_id' => 13,
            'is_right' => true
        ));
        DB::table('answer')->
        insert(array(
            'text' => '.cpp',
            'question_id' => 13,
            'is_right' => true
        ));
//-------------------------------------------------------------------------

        DB::table('question')->
        insert(array(
            'type' => QuestionType::OpenOneString,
            'text' => "Каким будет результат выполнения приведённого фрагмента кода?",
            'complexity' => QuestionComplexity::Medium,
            'image' => 'images/questions/test_for.png',
            'time' => 35,
            'theme_id' => 1
        ));
        DB::table('answer')->
        insert(array(
            'text' => '12345678910',
            'question_id' => 14,
            'is_right' => true
        ));
    }

}