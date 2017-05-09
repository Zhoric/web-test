<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestEngine\AnswerChecker;

class SingleStringQuestionAnswerResultCalculationTest extends TestCase
{
    /**
     * Подсчёт оценки за ответ на открытый однострочный вопрос.
     * Ответ считается правильным, если он совпал хотя бы с одним из правильных вариантов.
     * Пример вопроса: Сколько будет 1 + 1 ?
     */
    public function testCalculation()
    {
        //Arrange

        $firstRightAnswer = new Answer();
        $secondRightAnswer = new Answer();
        $thirdRightAnswer = new Answer();

        $firstRightAnswer->setText("2");
        $secondRightAnswer->setText("два");
        $thirdRightAnswer->setText("two");

        $rightAnswers = array($firstRightAnswer, $secondRightAnswer, $thirdRightAnswer);

        //Act

        $firstResult = AnswerChecker::calculatePointsForSingleStringAnswer($rightAnswers, '2');
        $secondResult = AnswerChecker::calculatePointsForSingleStringAnswer($rightAnswers, 'два');
        $thirdResult = AnswerChecker::calculatePointsForSingleStringAnswer($rightAnswers, '3');
        $fourthResult = AnswerChecker::calculatePointsForSingleStringAnswer($rightAnswers, '');
        $fifthResult = AnswerChecker::calculatePointsForSingleStringAnswer($rightAnswers, 'ДВА');
        $sixthResult = AnswerChecker::calculatePointsForSingleStringAnswer($rightAnswers, 'twO');

        //Assert

        $this->assertEquals(100, $firstResult);
        $this->assertEquals(100, $secondResult);
        $this->assertEquals(0, $thirdResult);
        $this->assertEquals(0, $fourthResult);
        $this->assertEquals(100, $fifthResult);
        $this->assertEquals(100, $sixthResult);
    }
}
