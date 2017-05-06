<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestEngine\AnswerChecker;

class ClosedQuestionAnswerResultCalculationTest extends TestCase
{

    /**
     * Тест подсчёта оценки (в процентах) ответа на закрытый вопрос с несколькими вариантами ответа.
     */
    public function testCalculation()
    {
        //Arrange

        //Возможные ответы на вопрос - 2 правильных и 2 неправильных.
        $firstAnswer = new Answer();
        $firstAnswer->setId(1)->setIsRight(true);
        $secondAnswer = new Answer();
        $secondAnswer->setId(2)->setIsRight(false);
        $thirdAnswer = new Answer();
        $thirdAnswer->setId(3)->setIsRight(true);
        $fourthAnswer = new Answer();
        $fourthAnswer->setId(4)->setIsRight(false);


        //Act

        //Выбор 2 правильных и одного неправильного ответа
        $twoRightOneWrongMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array(1,3,4));

        //Выбор только 2 правильных ответов
        $twoRightOnlyMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array(1,3));

        //Выбор только неправильного ответа
        $wrongOnlyMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array(4));

        //Выбор всех ответов
        $allAnswersMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array(1,2,3,4));

        $noAnswersMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array());

        //Assert

        $this->assertEquals(50, $twoRightOneWrongMark);
        $this->assertEquals(100, $twoRightOnlyMark);
        $this->assertEquals(0, $wrongOnlyMark);
        $this->assertEquals(0, $allAnswersMark);
        $this->assertEquals(0, $noAnswersMark);
    }
}
