<?php

namespace TestEngine;

use DateTime;
use Doctrine\Common\Cache\PredisCache;
use Exception;
use Illuminate\Support\Facades\Redis;
use Managers\TestManager;
use Managers\TestResultManager;
use Test;

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
    const testResultPrefix = 'tr';
    const testEndTimePrefix = 'end';
    const answersQualityPrefix = 'aq';
    const answeredQuestionsIdsPrefix = 'aqid';
    const questionEndTimePrefix = 'qend';

    const dateSerializationFormat = 'Y-m-d H:i:s';
    const cacheExpiration = '+ 1 day';

    private static $_testManager;
    private static $_testResultManager;
    private static $_redisClient;

    /**
     * @return TestManager
     */
    public static function getTestManager(){
        if (self::$_testManager == null){
            self::$_testManager = app()->make(TestManager::class);
        }

        return self::$_testManager;
    }

    /**
     * @return TestResultManager
     */
    public static function getTestResultManager(){
        if (self::$_testResultManager == null){
            self::$_testResultManager = app()->make(TestResultManager::class);
        }

        return self::$_testResultManager;
    }

    /**
     * @return Redis
     */
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

        self::initializeSession($sessionId, $userId, $test);

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
        $session->setTestResultId($redis->get(self::testResultPrefix.$sessionId));
        $endTimeString = $redis->get(self::testEndTimePrefix.$sessionId);
        $session->setTestEndDateTime(date_create_from_format(self::dateSerializationFormat, $endTimeString));
        $qEndTimeString = $redis->get(self::questionEndTimePrefix.$sessionId);
        $session->setQuestionEndTime(date_create_from_format(self::dateSerializationFormat, $qEndTimeString));
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

    /**
     * Инициализация значений полей сессии тестирования в кэше.
     */
    private static function initializeSession($sessionId, $userId, Test $test){
        $testId = $test->getId();
        $testEndTime = $date = new DateTime('+'.$test->getTimeTotal().' seconds');
        $testEndTime = $testEndTime->format(self::dateSerializationFormat);
        $answersQuality = 0;
        $answeredQuestionsIds = [];

        self::setSessionTest($sessionId,$testId);
        self::setSessionUser($sessionId,$userId);
        self::setSessionTestResult($sessionId, $userId, $testId);
        self::setSessionAnsweredQuestions($sessionId, $answeredQuestionsIds);
        self::setSessionAnswersQuality($sessionId, $answersQuality);
        self::setSessionEndTime($sessionId, $testEndTime);
    }

    public static function setSessionUser($sessionId, $userId){
        $userIdKey = self::userIdPrefix.$sessionId;

        self::getRedisClient()->set($userIdKey, $userId);
        self::getRedisClient()->expireat($userIdKey, strtotime(self::cacheExpiration));
    }

    public static function setSessionTest($sessionId, $testId){
        $testIdKey = self::testIdPrefix.$sessionId;

        self::getRedisClient()->set($testIdKey, $testId);
        self::getRedisClient()->expireat($testIdKey, strtotime(self::cacheExpiration));
    }

    public static function setSessionTestResult($sessionId, $userId, $testId){
        $testResultIdKey = self::testResultPrefix.$sessionId;
        $testResultId = self::getTestResultManager()->createEmptyTestResult($userId, $testId);

        self::getRedisClient()->set($testResultIdKey, $testResultId);
        self::getRedisClient()->expireat($testResultIdKey, strtotime(self::cacheExpiration));
    }

    public static function setSessionEndTime($sessionId, $testEndTime){
        $endTimeKey = self::testEndTimePrefix.$sessionId;

        self::getRedisClient()->set($endTimeKey, $testEndTime);
        self::getRedisClient()->expireat($endTimeKey, strtotime(self::cacheExpiration));
    }

    public static function setSessionAnswersQuality($sessionId, $answersQuality){
        $qualityKey = self::answersQualityPrefix.$sessionId;

        self::getRedisClient()->set($qualityKey, $answersQuality);
        self::getRedisClient()->expireat($qualityKey, strtotime(self::cacheExpiration));
    }

    public static function setSessionAnsweredQuestions($sessionId, $answeredQuestionsIds){
        $answeredKey = self::answeredQuestionsIdsPrefix.$sessionId;

        self::getRedisClient()->set($answeredKey, implode($answeredQuestionsIds, ','));
        self::getRedisClient()->expireat($answeredKey, strtotime(self::cacheExpiration));
    }

    public static function setSessionQuestionEndTime($sessionId, $questionEndTime){
        $questionEndTimeKey = self::questionEndTimePrefix.$sessionId;

        self::getRedisClient()->set($questionEndTimeKey, $questionEndTime);
        self::getRedisClient()->expireat($questionEndTimeKey, strtotime(self::cacheExpiration));

    }




}