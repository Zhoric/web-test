<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use TestEngine\TestResultCalculator;

class TestResultCalculationTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        DB::table('test_settings')->truncate();
        $this->seed('SettingsTableSeeder');
    }

    /**
     * Получение объекта ответа на вопрос с заданными параметрами.
     * @param $complexity - Сложность вопроса.
     * @param $type - Тип вопроса
     * @param $rightPercentage - Степень правильности ответа (в процентах).
     * @return GivenAnswer
     */
    private function getQuestionAnswer($complexity, $type, $rightPercentage){
        $question = new Question();
        $question->setComplexity($complexity)
            ->setType($type);

        $answer = new GivenAnswer();
        $answer->setRightPercentage($rightPercentage)
            ->setQuestion($question);

        return $answer;
    }
    /**
     * Расчёт общего результата за тест по оценкам за ответы.
     */
    public function testCalculateTestResultShouldReturnCorrectValue()
    {
        //Arrange
        $easyClosed70pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, 70);
        $easyClosed100pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, 100);
        $mediumClosed100pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Medium, QuestionType::ClosedManyAnswers, 100);
        $mediumClosed70pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Medium, QuestionType::ClosedOneAnswer, 70);
        $hardOneString30pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::High, QuestionType::OpenOneString, 30);

        //Act
        $result = TestResultCalculator::calculateTestResult(
            array($easyClosed70pointsAnswer,
                $easyClosed100pointsAnswer,
                $mediumClosed100pointsAnswer,
                $mediumClosed70pointsAnswer,
                $hardOneString30pointsAnswer));

        //Assert
        $this->assertEquals(72, $result);
    }

    /**
     * Метод подсчёта результата должен возвращать null, если в тесте есть непроверенный открытый вопрос.
     * (т.к. для подсчёта требуется проверка преподавателем).
     */
    public function testCalculateTestResultShouldReturnNullIfContainsOpenQuestion(){
        //Arrange
        $easyClosed70pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, 70);
        $easyClosed100pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, 100);
        $mediumClosed100pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Medium, QuestionType::ClosedManyAnswers, 100);
        $mediumOpenNoPointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Medium, QuestionType::OpenManyStrings, null);

        //Act
        $result = TestResultCalculator::calculateTestResult(
            array($easyClosed70pointsAnswer,
                $easyClosed100pointsAnswer,
                $mediumClosed100pointsAnswer,
                $mediumOpenNoPointsAnswer));

        //Assert
        $this->assertNull($result);
    }

    /**
     * Метод подсчёта результата должен возвращать null, если в тесте есть непроверенный вопрос с программным кодом.
     * (т.к. для подсчёта требуется асинхронная проверка модулем вопросов с программным кодом).
     */
    public function testCalculateTestResultShouldReturnNullIfContainsProgramQuestion(){
        //Arrange
        $easyClosed70pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, 70);
        $easyClosed100pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, 100);
        $mediumClosed100pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Medium, QuestionType::ClosedManyAnswers, 100);
        $hardProgramNoPointsAnswer = $this->getQuestionAnswer(QuestionComplexity::High, QuestionType::WithProgram, null);

        //Act
        $result = TestResultCalculator::calculateTestResult(
            array($easyClosed70pointsAnswer,
                $easyClosed100pointsAnswer,
                $mediumClosed100pointsAnswer,
                $hardProgramNoPointsAnswer));

        //Assert
        $this->assertNull($result);
    }

    /**
     * При подсчёте итоговой оценки за тест вопросы, которые могут быть проверены
     * автоматически (все, кроме открытого и вопроса с кодом), имеющие оценку null (ответ не был дан)
     * должны быть посчитаны за 0 баллов.
     */
    public function testCalculateTestResultShouldReturnZeroForNotAnsweredQuestions(){
        //Arrange
        $easyClosedNoAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, null);
        $mediumClosedNoAnswer = $this->getQuestionAnswer(QuestionComplexity::Medium, QuestionType::ClosedOneAnswer, null);
        $hardClosedNoAnswer = $this->getQuestionAnswer(QuestionComplexity::High, QuestionType::ClosedManyAnswers, null);

        //Act
        $result = TestResultCalculator::calculateTestResult(
            array($easyClosedNoAnswer,
                $mediumClosedNoAnswer,
                $hardClosedNoAnswer), true);

        //Assert
        $this->assertEquals(0, $result);
    }

    /**
     * Метод подсчёта итоговой оценки должен выбрасывать исключение, если у какого-либо вопроса не указана сложность.
     * @expectedException Exception
     * @expectedExceptionMessage Не указана сложность вопроса
     */
    public function testCalculateTestResultShouldThrowExceptionIfAnyQuestionComplexityNotSpecified(){
        //Arrange
        $easyClosed70pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, 70);
        $easyClosed100pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Low, QuestionType::ClosedOneAnswer, 100);
        $mediumClosed100pointsAnswer = $this->getQuestionAnswer(QuestionComplexity::Medium, QuestionType::ClosedManyAnswers, 100);
        $undefinedComplexityQuestionAnswer = $this->getQuestionAnswer(null, QuestionType::ClosedManyAnswers, 60);

        //Act & Assert
        TestResultCalculator::calculateTestResult(
            array($easyClosed70pointsAnswer,
                $easyClosed100pointsAnswer,
                $mediumClosed100pointsAnswer,
                $undefinedComplexityQuestionAnswer));
    }


}
