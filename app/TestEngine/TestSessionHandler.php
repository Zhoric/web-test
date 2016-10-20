<?php

namespace TestEngine;

use Exception;
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
    const cacheExpiration = '+ 1 day';

    private static $_testManager;
    private static $_redisClient;

    public static function getTestManager(){
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
            throw new Exception("Ошибка при создании сессии. Тест с id ".$testId." не найден!");
        }

        $sessionId = self::generateSessionId($userId, $testId);

        $testEndTime = $date = new \DateTime('+'.$test->getTimeTotal().' seconds');
        $testEndTime = $testEndTime->format(self::dateSerializationFormat);
        $answersQuality = 0;
        $answeredQuestionsIds = [];

        $redis = self::getRedisClient();

        $userIdKey = self::userIdPrefix.$sessionId;
        $testIdKey = self::testIdPrefix.$sessionId;
        $endTimeKey = self::testEndTimePrefix.$sessionId;
        $qualityKey = self::answersQualityPrefix.$sessionId;
        $answeredKey = self::answeredQuestionsIdsPrefix.$sessionId;

        $redis->set($userIdKey, $userId);
        $redis->expireat($userIdKey, strtotime(self::cacheExpiration));
        $redis->set($testIdKey, $testId);
        $redis->expireat($testIdKey, strtotime(self::cacheExpiration));
        $redis->set($endTimeKey, $testEndTime);
        $redis->expireat($endTimeKey, strtotime(self::cacheExpiration));
        $redis->set($qualityKey, $answersQuality);
        $redis->expireat($qualityKey, strtotime(self::cacheExpiration));
        $redis->set($answeredKey, implode($answeredQuestionsIds, ','));
        $redis->expireat($answeredKey, strtotime(self::cacheExpiration));

        return $sessionId;
    }

    /**
     * Получение объекта сессии тестирования по идентификатору сессии.
     */
    public static function getSession($sessionId){
        $redis = self::getRedisClient();

        $session = new TestSession();
        $session->setSessionId($sessionId);
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

    public static function updateSession($sessionId, $answeredQuestionsIds, $quality = 0){
        $redis = self::getRedisClient();

        $qualityKey = self::answersQualityPrefix.$sessionId;
        $answeredKey = self::answeredQuestionsIdsPrefix.$sessionId;

        $redis->set($qualityKey, $quality);
        $redis->expireat($qualityKey, strtotime(self::cacheExpiration));
        $redis->set($answeredKey, implode($answeredQuestionsIds, ','));
        $redis->expireat($answeredKey, strtotime(self::cacheExpiration));
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