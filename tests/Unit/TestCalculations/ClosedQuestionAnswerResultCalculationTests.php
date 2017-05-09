<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestEngine\AnswerChecker;

class ClosedQuestionAnswerResultCalculationTest extends TestCase
{

    /**
     * Метод подсчёта оценки на закрытый вопрос должен возвращать 0, если не было выбрано ни одного ответа.
     */
    public function testCalculatePointsForClosedAnswerShouldReturn0IfNoAnswersGiven()
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


        $noAnswersMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array());

        //Assert
        $this->assertEquals(0, $noAnswersMark);
    }

    //Выбор только 2 правильных ответов
    public function testCalculatePointsForClosedAnswerShouldReturn100IfAllRightAnswersSelected()
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
        $twoRightOnlyMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array(1,3));

        //Assert
        $this->assertEquals(100, $twoRightOnlyMark);
    }

    //Выбор только неправильного ответа
    public function testCalculatePointsForClosedAnswerShouldReturn0IfNoRightAnswersSelected(){
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
        $wrongOnlyMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array(4));

        //Assert
        $this->assertEquals(0, $wrongOnlyMark);
    }

    /**
     * За выбор неправильных вариантов ответов должны сниматься баллы.
     */
    public function testCalculatePointsForClosedAnswerShouldDecreaseMarkForWrongAnswersSelected(){
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
        //Выбор 2 правильных и одного неправильного ответа (50 + 50 - 50 = 50)
        $twoRightOneWrongMark = AnswerChecker::calculatePointsForClosedAnswer(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer), array(1,3,4));

        //Assert
        $this->assertEquals(50, $twoRightOneWrongMark);
    }

}
