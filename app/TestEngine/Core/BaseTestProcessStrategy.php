<?php
namespace TestEngine;

use Exception;
use GivenAnswer;
use Managers\QuestionManager;
use Managers\SettingsManager;
use Managers\TestManager;
use Managers\TestResultManager;
use QuestionType;
use QuestionViewModel;
use Repositories\UnitOfWork;
use TestResult;
use TestType;

/**
 * Базовый класс стратегии процесса тестирования.
 * Class BaseTestProcessStrategy
 * @package TestEngine
 */
class BaseTestProcessStrategy
{
    /**
     * @var TestManager
     */
    protected $_testManager;

    /**
     * @var TestResultManager
     */
    protected $_testResultManager;

    /**
     * @var QuestionManager
     */
    protected $_questionManager;

    /**
     * @var SettingsManager
     */
    protected $_settingsManager;

    /**
     * @var TestSessionFactory
     */
    protected $_sessionFactory;

    /**
     * @var TestSessionTracker
     */
    protected $_testSessionTracker;

    /**
     * @var TestSession
     */
    protected $_testSession;

    /**
     * @var TestResult
     */
    protected $_testResult;

    /**
     * @var TestType
     */
    protected $_testType;

    /**
     * Обработка окончания теста. Подсчёт и сохранение результатов.
     */
    public function processTestEnd(){
        $testResultId = $this->_testSession->getTestResultId();
        $answeredQuestions = $this->_testSession->getAnsweredQuestionsIds();

        //Если не был дан ответ ни на один из вопросов, попытка не будет учтена.
        if (isset($answeredQuestions) && count ($answeredQuestions) != 0){
            $this->calculateAndSaveResult($testResultId);
        } else {
            $this->_testResultManager->delete($testResultId);
        }

        $sessionId = $this->_testSession->getSessionId();
        $this->_testSessionTracker->finalizeSession($sessionId);
    }

    /**
     * Подсчёт и сохранение оценки за тест.
     * @param $testResultId
     */
    public function calculateAndSaveResult($testResultId){

        $testResultMark = TestResultCalculator::calculate($testResultId);

        $testResult = $this->_testResultManager->getById($testResultId);
        $testResult->setMark($testResultMark);

        $this->_testResult = $testResult;
        $this->_testResultManager->update($testResult);
    }

    /**
     * Подсчёт количества секунд, оставшихся до конца тестирования.
     */
    protected function getTimeLeftBeforeTestEnd(){
        $now = date(GlobalTestSettings::dateSerializationFormat);
        $testTimeLeft = $this->_testSession->getTestEndDateTime();

        $secondsLeft = strtotime($testTimeLeft) - strtotime($now);
        return $secondsLeft;
    }

    /**
     * Подсчёт количества секунд, оставшихся на ответ на текущий вопрос.
     */
    protected function getTimeLeftBeforeQuestionEnd(){
        $now = date(GlobalTestSettings::dateSerializationFormat);
        $questionTimeLeft = $this->_testSession->getQuestionEndTime();

        $secondsLeft = strtotime($questionTimeLeft) - strtotime($now);
        return $secondsLeft;
    }

    /**
     * Выбор случайного вопроса из списка подходящих.
     */
    protected function getRandomQuestion(TestSession $testSession){
        $answeredQuestions = $testSession->getAnsweredQuestionsIds();
        $allQuestions = $testSession->getAllQuestionsIds();

        $notAnsweredQuestions = array_diff($allQuestions, $answeredQuestions);

        array_flatten($notAnsweredQuestions);
        $nextQuestionIndex = array_rand($notAnsweredQuestions);
        $nextQuestionId = $notAnsweredQuestions[$nextQuestionIndex];

        return $nextQuestionId;
    }

    /**
     * Валидация сессии тестирования.
     * Проверка истечения времени на текущий вопрос и на тест в целом.
     * @param null $questionId
     * @throws Exception
     */
    protected function validateTime($questionId = null){
        $endTime = $this->_testSession->getTestEndDateTime();
        if ($endTime == null || $endTime == ''){
            throw new Exception('Не найдена сессия тестирования!');
        }

        $timeLeftToEnd = $this->getTimeLeftBeforeTestEnd();
        $testEndTolerance = $this->_settingsManager->get(GlobalTestSettings::testEndToleranceKey);
        $questionEndTolerance = $this->_settingsManager->get(GlobalTestSettings::questionEndToleranceKey);

        if ($testEndTolerance + $timeLeftToEnd <= 0){
            $this->processTestEnd();
            throw new Exception('Время, отведённое на тест истекло!');
        }
        if ($questionId != null){
            $timeLeftToQuestion = $this->getTimeLeftBeforeQuestionEnd();
            if ($questionEndTolerance + $timeLeftToQuestion <= 0){
                $this->saveQuestionAnswer($this->_testSession, $questionId, 0);
                throw new Exception('Время, отведённое на данный вопрос истекло. Ответ не будет засчитан!');
            }
        }
    }

    /**
     * Проверка на то, что мы дважды не отвечаем на один и тот же вопрос
     * @param $questionId - id вопроса, на который даётся ответ.
     * @throws Exception
     */
    protected function validateQuestionToAnswer($questionId){
        $answeredQuestionsIds = $this->_testSession->getAnsweredQuestionsIds();

        if (in_array($questionId, $answeredQuestionsIds) &&
            $questionId != $answeredQuestionsIds[count($answeredQuestionsIds) - 1]){
            throw new Exception('Вы уже отвечали на этот вопрос!');
        }
    }

    /**
     * Проверка номера попытки прохождения теста.
     * @param $userId
     * @param $testId
     * @throws Exception
     */
    protected function validateAttemptNumber($userId, $testId){
        $test = $this->_testManager->getById($testId);

        $attemptsAllowed = $test->getAttempts();

        $extraAttempts = $this->_testResultManager->getExtraAttemptsCount($userId, $testId);
        $lastAttempt = $this->_testManager->getTestAttemptsUsedCount($testId, $userId);

        if ($attemptsAllowed != 0 && $lastAttempt >= $attemptsAllowed + $extraAttempts){
            throw new Exception('Все попытки прохождения теста исчерпаны!');
        }
    }

    /**
     * Проверка правильности ответов
     * @param QuestionViewModel $questionInfo - Вопрос со всеми его ответами.
     * @param QuestionAnswer $questionAnswer - ответы, которые дал студент
     * @param TestResult $testResult - айдишник результата теста(нужен в случаях, если вопрос с кодом)
     * @return int - оценка за ответ, %
     */
    protected function checkAnswers($questionInfo, $questionAnswer, $testResult = null){
        $answerResultPoints = null;

        $question = $questionInfo->getQuestion();
        $questionType = $question->getType();
        $answers = $questionInfo->getAnswers();
        switch ($questionType){
            case QuestionType::ClosedOneAnswer:
            case QuestionType::ClosedManyAnswers: {

            $studentAnswers = $questionAnswer->getAnswerIds();
                $answerResultPoints =  AnswerChecker::calculatePointsForClosedAnswer($answers, $studentAnswers);
                break;
            }
            case QuestionType::OpenOneString:{
                $answerText = $questionAnswer->getAnswerText();
                $answerResultPoints = AnswerChecker::calculatePointsForSingleStringAnswer($answers, $answerText);
                break;
            }
            case QuestionType::WithProgram:{
                $studentCode = $questionAnswer->getAnswerText();
                $questionId = $question->getId();
                $answerResultPoints = AnswerChecker::calculatePointsForProgramAnswer($questionId, $studentCode, $testResult);
                break;
            }
        }

        return $answerResultPoints;
    }

    /**
     * Сохранение ответа студента в БД.
     * @param TestSession $session - сессия тестирования.
     * @param $questionId - вопрос, на который дан ответ.
     * @param $rightPercentage - степень правильности ответа.
     * @param string $answerText - текст ответа.
     * @throws \Exception
     */
    protected function saveQuestionAnswer($session, $questionId, $rightPercentage, $answerText = ''){
        $testResultId = $session->getTestResultId();
        $testResult = $this->_testResultManager->getById($testResultId);
        $question = $this->_questionManager->getById($questionId);

        if ($testResultId == null || $question == null){
            throw new Exception('Не удалось сохранить ответ!');
        }

        $answeredQuestionsIds = $this->_questionManager->getGivenAnswersIds($testResultId);

        if (in_array($question->getId(), $answeredQuestionsIds)){
            throw new Exception("На данный вопрос уже был дан ответ.");
        }

        //todo:: для вопроса с программой сущность ответа на вопрос создается ранее
        if($question->getType() != QuestionType::WithProgram) {
            $givenAnswer = new GivenAnswer();
            $givenAnswer->setTestResult($testResult);
            $givenAnswer->setQuestion($question);
            $givenAnswer->setRightPercentage($rightPercentage);
            $givenAnswer->setAnswer($answerText);
            $this->_questionManager->createQuestionAnswer($givenAnswer);
        }


    }

    /**
     * Получение текста ответа на вопрос.
     * В случае, если вопрос закрытый - в текст ответа копируется текст всех выбранных вариантов.
     * Если вопрос открытый, то в текст ответа будет сохранён текст, введённый студентом при ответе.
     * @param QuestionViewModel $questionAnswer - вопрос со всеми возможными ответами.
     * @param QuestionAnswer $studentAnswer - ответ студента
     * @return string
     * @internal param Question $question
     */
    protected function getAnswerText($questionAnswer, $studentAnswer){

        $openAnswerText = $studentAnswer->getAnswerText();

        if ($openAnswerText != null && $openAnswerText != ""){
            return $openAnswerText.' ';
        }

        $studentAnswersIds = $studentAnswer->getAnswerIds();
        $questionType = $questionAnswer->getQuestion()->getType();
        $answers = $questionAnswer->getAnswers();
        $answerText = '';

        switch ($questionType){
            case QuestionType::ClosedOneAnswer: {
                if (count($studentAnswersIds) > 0){
                    $studentAnswerId = $studentAnswersIds[0];
                    $selectedAnswers = array_filter($answers, function($answer) use($studentAnswerId){
                        return $answer['id'] == $studentAnswerId;
                    });
                    if ($selectedAnswers != null && count($selectedAnswers) > 0){
                        $answerText = array_values($selectedAnswers)[0]['text'].' ';
                    }
                }
                break;
            }
            case QuestionType::ClosedManyAnswers: {
                foreach ($studentAnswersIds as $studentAnswerId){
                    $selectedAnswers = array_filter($answers, function($answer) use($studentAnswerId){
                        return $answer['id'] == $studentAnswerId;
                    });

                    if ($selectedAnswers != null && count($selectedAnswers) > 0){
                        $answerText .= array_values($selectedAnswers)[0]['text'].' </answer>';
                    }
                }
                break;
            }
        }

        return $answerText;
    }

    /**
     * Проверка наличия неотвеченных вопросов теста.
     * При отсутствии таковых, тест будет завершен.
     */
    protected function checkIfNotAnsweredQuestionsExists(){
        $answered = $this->_testSession->getAnsweredQuestionsIds();
        $all = $this->_testSession->getAllQuestionsIds();

        if (count($answered) === count($all)){
            $this->processTestEnd();
        }
    }
}