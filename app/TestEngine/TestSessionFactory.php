<?php

namespace TestEngine;

use DateTime;
use Exception;
use Illuminate\Redis\Database;
use Managers\QuestionManager;
use Managers\TestManager;
use Managers\TestResultManager;
use Test;

class TestSessionFactory
{
    const dateSerializationFormat = 'Y-m-d H:i:s';

    /**
     * @var Database
     */
    protected $redisClient;

    /**
     * @var TestResultManager
     */
    protected $testResultManager;

    /**
     * @var QuestionManager
     */
    protected $questionManager;

    /**
     * @var TestManager
     */
    protected $testManager;

    public function __construct(Database $redisClient,
                                TestResultManager $testResultManager,
                                TestManager $testManager,
                                QuestionManager $questionManager)
    {
        $this->redisClient = $redisClient;
        $this->testManager = $testManager;
        $this->testResultManager = $testResultManager;
        //TODO[NZ]: Убрать костыльное инстанцирование.
        $this->redisClient = app()->make('redis');
    }

    /**
     * Получение сессии тестирования по идентификатору.
     * @param $sessionId
     * @return TestSession
     * @throws Exception
     */
    public function getBySessionId($sessionId){
        $testSession = new TestSession($sessionId, $this->redisClient);
        $userId = $testSession->getUserId();
        if (!isset($userId)){
            throw new Exception('Ошибка! Указанная сессия тестирования не найдена!');
        }
        return $testSession;
    }

    /**
     * Инициализация и получение сессии тестирования.
     * @param $userId - Идентификатор пользователя.
     * @param $testId - Идентификатор теста.
     * @return TestSession - Возвращает проинициализированный экземпляр сессии тестирования.
     * @throws Exception
     */
    public function getInitialized($userId, $testId){
        $sessionId = $userId.$testId;

        $testResultId = $this->testResultManager->createEmptyTestResult($userId, $testId);
        $test = $this->testManager->getById($testId);
        if (!isset($test)){
            throw new Exception('Невозможно начать тестирование! Указанный тест не найден!');
        }
        $testEndTime = $date = new DateTime('+'.$test->getTimeTotal().' seconds');

        $testEndTime = $testEndTime->format(self::dateSerializationFormat);

        $session = new TestSession($sessionId, $this->redisClient);
        $session->setUserId($userId);
        $session->setTestId($testId);
        $session->setTestResultId($testResultId);
        $session->setAnswersQuality(0);
        $session->setAnsweredQuestionsIds([]);
        $session->setTestEndDateTime($testEndTime);

        $session->setAllQuestionsIds($this->getQuestionsForTestProcess($test));

        return $session;
    }

    /**
     * Выбор вопросов теста, которые будут представлены студенту.
     * @param Test $test
     * @return array
     * @throws Exception
     */
    private function getQuestionsForTestProcess(Test $test){
        $testQuestionsIds = [];
        $questionsTotalDuration = 0;

        //Получаем время, отведённое на тест
        $testDuration = $test->getTimeTotal();
        $allQuestions = $this->testManager->getQuestionsByTest($test->getId());

        //Перемешиваем вопросы теста
        shuffle($allQuestions);
        for ($i = 0; $i < count($allQuestions); $i++){
            $currentQuestionDuration = $allQuestions[$i]->getTime();
            $durationEstimation = $currentQuestionDuration + $questionsTotalDuration;

            //Если вопрос "помещается" по времени, добавляем в список.
            if ($durationEstimation <= $testDuration){
                array_push($testQuestionsIds, $allQuestions[$i]->getId());
                $questionsTotalDuration += $currentQuestionDuration;
            }
        }

        if (count($testQuestionsIds) == 0){
            throw new Exception('Ошибка! Не удалось сформировать список вопросов теста!');
        }

        return $testQuestionsIds;
    }
}