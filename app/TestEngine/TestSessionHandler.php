<?php

namespace TestEngine;

use Managers\TestManager;

/**
 * Класс, отвечающий за работу с сессиями тестирования.
 */
class TestSessionHandler
{
    /**
     * Префиксы ключей для хранения в кэше полей сессии тестирования.
     */
    const userIdPrefix = 'uid';
    const testIdPrefix = 'tid';
    const testEndTimePrefix = 'end';
    const answersQualityPrefix = 'aq';
    const answeredQuestionsIdsPrefix = 'aqid';
    const dateSerializationFormat = 'Y-m-d H:i:s';

    private static $_testManager;
    private static $_redisClient;

    private static function getTestManager(){
        if (self::$_testManager == null){
            self::$_testManager = app()->make(TestManager::class);
        }

        return self::$_testManager;
    }

    private static function getRedisClient(){
        if (self::$_redisClient == null){
            self::$_redisClient = app()->make('redis');
        }

        return self::$_redisClient;
    }

    /**
     * Создание сессии теста.
     */
    public static function createTestSession($testId, $userId){
        $test = self::getTestManager()->getById($testId);

        if ($test == null){
            throw new \Exception("Ошибка при создании сессии. Тест с id ".$testId." не найден!");
        }

        $sessionId = self::generateSessionId($userId, $testId);

        $testEndTime = $date = new \DateTime('+'.$test->getTimeTotal().' seconds');
        $testEndTime = $testEndTime->format(self::dateSerializationFormat);
        $answersQuality = 0;
        $answeredQuestionsIds = [];

        $redis = self::getRedisClient();

        $redis->set(self::userIdPrefix.$sessionId, $userId);
        $redis->set(self::testIdPrefix.$sessionId, $testId);
        $redis->set(self::testEndTimePrefix.$sessionId, $testEndTime);
        $redis->set(self::answersQualityPrefix.$sessionId, $answersQuality);
        $redis->set(self::answeredQuestionsIdsPrefix.$sessionId, implode($answeredQuestionsIds, ','));

        return $sessionId;
    }

    /**
     * Получение объекта сессии тестирования по идентификатору сессии
     */
    public static function getSession($sessionId){
        $redis = self::getRedisClient();

        $session = new TestSession();
        $session->setUserId($redis->get(self::userIdPrefix.$sessionId));
        $session->setTestId($redis->get(self::testIdPrefix.$sessionId));
        $endTimeString = $redis->get(self::testEndTimePrefix.$sessionId);
        $session->setTestEndDateTime(date_create_from_format(self::dateSerializationFormat, $endTimeString));
        $session->setAnswersQuality($redis->get(self::answersQualityPrefix.$sessionId));
        $answeredQuestions = $redis->get(self::answeredQuestionsIdsPrefix.$sessionId);
        $answeredQuestions = $answeredQuestions == null ? [] : explode(',', $answeredQuestions);
        $session->setAnsweredQuestionsIds($answeredQuestions);

        return $session;
    }

    /**
    * Генерация идентификатора сессии на основе Id теста, Id студента и текущего времени.
    */
    private static function generateSessionId($userId, $testId){
        $currentDate = date(self::dateSerializationFormat);
        $sessionDataString = $userId.$testId.$currentDate;
        return bcrypt($sessionDataString);
    }


}