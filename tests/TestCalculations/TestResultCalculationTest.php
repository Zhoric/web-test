<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestEngine\TestResultCalculator;

class TestResultCalculationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCalculation()
    {
        //Arrange
        $firstQuestion = new Question();
        $firstQuestion->setComplexity(QuestionComplexity::Low);
        $firstAnswer = new GivenAnswer();
        $firstAnswer->setRightPercentage(70)->setQuestion($firstQuestion);

        $secondQuestion = new Question();
        $secondQuestion->setComplexity(QuestionComplexity::Low);
        $secondAnswer = new GivenAnswer();
        $secondAnswer->setRightPercentage(100)->setQuestion($firstQuestion);

        $thirdQuestion = new Question();
        $thirdQuestion->setComplexity(QuestionComplexity::Medium);
        $thirdAnswer = new GivenAnswer();
        $thirdAnswer->setRightPercentage(100)->setQuestion($firstQuestion);

        $fourthQuestion = new Question();
        $fourthQuestion->setComplexity(QuestionComplexity::Medium);
        $fourthAnswer = new GivenAnswer();
        $fourthAnswer->setRightPercentage(70)->setQuestion($firstQuestion);

        $fifthQuestion = new Question();
        $fifthQuestion->setComplexity(QuestionComplexity::High);
        $fifthAnswer = new GivenAnswer();
        $fifthAnswer->setRightPercentage(30)->setQuestion($firstQuestion);

        //Act
        $result = TestResultCalculator::calculateTestResult(
            array($firstAnswer, $secondAnswer, $thirdAnswer, $fourthAnswer, $fifthAnswer));

        //Assert
        $this->assertEquals(74, $result);
    }
}
