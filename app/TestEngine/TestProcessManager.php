<?php

namespace TestEngine;
use League\Flysystem\Exception;
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
     * @var TestSession
     */
    private static $_session;

    /**
     * Инициализация процесса тестирования.
     */
    public static function initTest($userId, $testId){
        //TODO: проверка номера попытки
        $sessionId = TestSessionHandler::createTestSession($userId, $testId);
        return $sessionId;
    }

    /**
     * Получение следующего вопроса на основании настроек теста
     * и списка вопросов, на которые уже были даны ответы.
     */
    //TODO: НЕ ВОЗВРАЩАТЬ В ОТВЕТАХ is_right !
    public static function getNextQuestion($sessionId){
        try{
            self::$_testManager = TestSessionHandler::getTestManager();

            self::$_session = TestSessionHandler::getSession($sessionId);
            self::validateTestSession();

            $suitableQuestions = self::getSuitableQuestionsIds();
            self::validateSuitableQuestions($suitableQuestions);

            $nextQuestionId = self::getRandomQuestion($suitableQuestions);
            self::updateTestSession($nextQuestionId);

        } catch (Exception $exception){
            return array('message' => $exception->getMessage());
        }

        return self::$_testManager->getQuestionWithAnswers($nextQuestionId);
    }

    /**
     * Обработка ответа студента на вопрос теста.
     * @param $sessionId - Идентификатор сессии.
     * @param QuestionAnswer $questionAnswer - Ответ на вопрос теста.
     */
    public function processAnswer($sessionId, QuestionAnswer $questionAnswer){
        
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
     */
    private static function updateTestSession($nextQuestionId){
        $answeredQuestionsIds = self::$_session->getAnsweredQuestionsIds();
        array_push($answeredQuestionsIds, $nextQuestionId);
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


}