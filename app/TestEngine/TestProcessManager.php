<?php

namespace TestEngine;
use Exception;
use GivenAnswer;
use Managers\QuestionManager;
use Managers\TestManager;

/**
 * Класс, ответственный за управление процессом тестирования.
 */
class TestProcessManager
{
    const dateFormat = 'Y-m-d H:i:s';

    /**
     * Допуск времени (в секундах), отведённого на тест.
     */
    const testEndTolerance = 30;

    /**
     * @var TestManager
     */
    private static $_testManager;

    /**
     * @var QuestionManager
     */
    private static $_questionManager;

    /**
     * @var TestSession
     */
    private static $_session;

    /**
     * @return QuestionManager
     */
    private static function getQuestionManager(){
        if (self::$_questionManager == null){
            self::$_questionManager = app()->make(QuestionManager::class);
        }

        return self::$_questionManager;
    }

    /**
     * @return TestManager
     */
    private static function getTestManager(){
        if (self::$_testManager == null){
            self::$_testManager = app()->make(TestManager::class);
        }

        return self::$_testManager;
    }

    /**
     * Инициализация процесса тестирования.
     */
    public static function initTest($userId, $testId)
    {
        //self::validateAttemptNumber($userId, $testId);

        $sessionId = TestSessionHandler::createTestSession($userId, $testId);
        return $sessionId;
    }

    /**
     * Получение следующего вопроса на основании настроек теста
     * и списка вопросов, на которые уже были даны ответы.
     */
    public static function getNextQuestion($sessionId){
        try{
            self::$_testManager = TestSessionHandler::getTestManager();

            self::$_session = TestSessionHandler::getSession($sessionId);
            self::validateTestSession();

            $suitableQuestions = self::getSuitableQuestionsIds();
            self::validateSuitableQuestions($suitableQuestions);

            $nextQuestionId = self::getRandomQuestion($suitableQuestions);

        } catch (Exception $exception){
            return array('message' => $exception->getMessage());
        }

        return self::$_testManager->getQuestionWithAnswers($nextQuestionId, false);
    }

    /**
     * Обработка ответа студента на вопрос теста.
     * @param $sessionId - Идентификатор сессии.
     * @param QuestionAnswer $questionAnswer - Ответ на вопрос теста.
     * @return array
     */
    public static function processAnswer($sessionId, QuestionAnswer $questionAnswer){
        try{
            $session = TestSessionHandler::getSession($sessionId);
            self::$_session = $session;
            self::validateTestSession();

            $testResultId = $session->getTestResultId();
            $questionId = $questionAnswer->getQuestionId();
            self::validateQuestionToAnswer($questionId);

            $question = self::getQuestionManager()->getWithAnswers($questionId);
            $answers = $question->getAnswers();

            $answerRightPercentage = self::checkAnswers($answers,
                $questionAnswer->getAnswerIds());

            self::saveQuestionAnswer($session, $questionId, $answerRightPercentage);
            self::updateTestSession($questionId);

        } catch (Exception $exception){
            return array('message' => 'Ошибка при обработке ответа: '.$exception->getMessage());
        }
    }

    /**
     * Получение идентификаторов вопросов, подходящих под условия,
     * настройки и текущее состояние процесса тестирования.
     */
    private static function getSuitableQuestionsIds(){
        $testManager = self::$_testManager;
        $session = self::$_session;

        $testId = $session->getTestId();
        $test = $testManager->getById($testId);
        $answeredQuestionsIds = $session->getAnsweredQuestionsIds();

        $timeLeft = self::getTimeLeftBeforeTestEnd();
        $suitableQuestionsIds = $testManager->getNotAnsweredQuestionsByTest(
            $testId,
            $answeredQuestionsIds,
            $timeLeft);

        return $suitableQuestionsIds;
    }

    /**
     * Подсчёт количества секунд, оставшихся до конца тестирования.
     */
    private static function getTimeLeftBeforeTestEnd(){
        $now = date(self::dateFormat);
        $testTimeLeft = self::$_session->getTestEndDateTime()->format(self::dateFormat);

        $secondsLeft = strtotime($testTimeLeft) - strtotime($now);
        return $secondsLeft;
    }

    /**
     * Выбор случайного вопроса из списка подходящих.
     */
    private static function getRandomQuestion($suitableQuestions){
        array_flatten($suitableQuestions);
        $nextQuestionIndex = array_rand($suitableQuestions);
        $nextQuestionId = $suitableQuestions[$nextQuestionIndex]['id'];

        return $nextQuestionId;
    }

    /**
     * Обновление состояния сессии тестирования.
     * @param $answeredQuestionId - id вопроса, на который был дан ответ.
     */
    private static function updateTestSession($answeredQuestionId){
        $answeredQuestionsIds = self::$_session->getAnsweredQuestionsIds();
        array_push($answeredQuestionsIds, $answeredQuestionId);
        self::$_session->setAnsweredQuestionsIds($answeredQuestionsIds);
        TestSessionHandler::updateSession(self::$_session->getSessionId(), $answeredQuestionsIds);
    }

    /**
     * Валидация сессии тестирования.
     */
    private static function validateTestSession(){
        $endTime = self::$_session->getTestEndDateTime();
        if ($endTime == null || $endTime == ''){
            throw new Exception('Не найдена сессия тестирования!');
        }

        $timeLeft = self::getTimeLeftBeforeTestEnd();
        if (self::testEndTolerance + $timeLeft <= 0){
            throw new Exception('Время, отведённое на тест истекло!');
        }
    }

    /**
     * Валидация списка подходящих вопросов.
     */
    private static function validateSuitableQuestions($suitableQuestionsIds){

        if ($suitableQuestionsIds == null || empty($suitableQuestionsIds)){
            throw new Exception('Тест завершен!');
        }
    }

    /**
     * Проверка на то, что мы дважды не отвечаем на один и тот же вопрос
     * @param $questionId - id вопроса, на который даётся ответ.
     * @throws Exception
     */
    private static function validateQuestionToAnswer($questionId){
        $answeredQuestionsIds = self::$_session->getAnsweredQuestionsIds();

        if (in_array($questionId, $answeredQuestionsIds)){
            throw new Exception('Вы уже отвечали на этот вопрос!');
        }
    }

    private static function validateAttemptNumber($userId, $testId){
        $test = self::getTestManager()->getById($testId);
        $attemptsAllowed = $test->getAttempts();

        $lastTestResultAttempt = self::getTestManager()->getTestAttemptsUsedCount($userId, $testId);

        if ($attemptsAllowed != 0 || $lastTestResultAttempt >= $attemptsAllowed){
            throw new Exception('Количество попыток прохождения теста исчерпано!');
        }
    }

    /**
     * Проверка правильности ответов
     * @param $answers - ответы вопроса
     * @param $studentAnswers - ответы, которые дал студент
     * @return int - оценка за ответ, %
     */
    private static function checkAnswers(array $answers, array $studentAnswers){
        return AnswerChecker::calculatePointsForAnswer($answers, $studentAnswers);
    }

    /**
     * Сохранение ответа студента в БД.
     * @param TestSession $session - сессия тестирования.
     * @param $questionId - вопрос, на который дан ответ.
     * @param $rightPercentage - степень правильности ответа.
     * @param string $answerText - текст ответа.
     * @throws \Exception
     */
    private static function saveQuestionAnswer($session, $questionId, $rightPercentage, $answerText = ''){
        $testResultId = $session->getTestResultId();
        $testResult = TestSessionHandler::getTestResultManager()->getById($testResultId);
        $question = self::getQuestionManager()->getById($questionId);

        if ($testResultId == null || $question == null){
            throw new Exception('Не удалось сохранить ответ!');
        }

        $givenAnswer = new GivenAnswer();
        $givenAnswer->setTestResult($testResult);
        $givenAnswer->setQuestion($question);
        $givenAnswer->setRightPercentage($rightPercentage);
        $givenAnswer->setAnswer($answerText);

        self::getQuestionManager()->createQuestionAnswer($givenAnswer);
    }


}