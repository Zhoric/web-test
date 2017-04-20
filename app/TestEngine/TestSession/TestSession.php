<?php

namespace TestEngine;

use Illuminate\Redis\Database;
use JsonSerializable;

/**
 * Класс, инкапсулирующий логику взаимодействия с сессией тестирования,
 * хранящейся в Redis Cache.
 * @package TestEngine
 */
class TestSession implements JsonSerializable
{
    /** ===== Префиксы ключей для хранения в кэше полей сессии тестирования ===== **/

    const userIdPrefix = 'uid';                 //   - ИДЕНТИФИКАТОР ПОЛЬЗОВАТЕЛЯ
    const testIdPrefix = 'tid';                 //   - ИДЕНТИФИКАТОР ТЕСТА
    const testResultPrefix = 'tr';              //   - ИДЕНТИФИКАТОР РЕЗУЛЬТАТА ТЕСТА
    const testEndTimePrefix = 'end';            //   - ВРЕМЯ ОКОНЧАНИЯ ТЕСТА
    const answersQualityPrefix = 'qu';          //   - СРЕДНЕЕ КАЧЕСТВО ОТВЕТОВ СТУДЕНТА [не используется]
    const answeredQuestionsIdsPrefix = 'aq';    //   - ИДЕНТИФИКАТОРЫ ВОПРОСОВ, НА КОТОРЫЕ УЖЕ ДАН ОТВЕТ
    const allQuestionsIdsPrefix = 'q';          //   - ИДЕНТИФИКАТОРЫ ВСЕХ ВОПРОСОВ ТЕСТА
    const questionEndTimePrefix = 'qend';       //   - ВРЕМЯ ОКОНЧАНИЯ ТЕКУЩЕГО ВОПРОСА ТЕСТА


    /** ========================================================================== **/

    /**
     * @var Database
     * Клиент хранилища Redis Cache.
     */
    private $_redisClient;

    /**
     * Идентификатор сессии.
     */
    private $sessionId;

    /**
     * TestSession constructor.
     * @param $sessionId - Идентификатор сессии.
     * @param Database $redisClient - Клиент Redis Cache.
     */
    public function __construct($sessionId, Database $redisClient)
    {
        $this->_redisClient = $redisClient;
        $this->sessionId = $sessionId;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * ИДЕНТИФИКАТОР ПОЛЬЗОВАТЕЛЯ, ПРОХОДЯШЕГО ТЕСТ.
     */
    public function getUserId()
    {
        return $this->_redisClient->get(self::userIdPrefix.$this->sessionId);
    }

    public function setUserId($userId)
    {
        $this->setWithDefaultExpiration(self::userIdPrefix, $userId);
    }

    /**
     * ИДЕНТИФИКАТОР ТЕСТА
     */
    public function getTestId()
    {
        return $this->_redisClient->get(self::testIdPrefix.$this->sessionId);
    }

    public function setTestId($testId)
    {
        $this->setWithDefaultExpiration(self::testIdPrefix, $testId);
    }

    /**
     * ВРЕМЯ ОКОНЧАНИЯ ПРОЦЕССА ТЕСТИРОВАНИЯ
     */
    public function getTestEndDateTime()
    {
        return $this->_redisClient->get(self::testEndTimePrefix.$this->sessionId);
    }

    public function setTestEndDateTime($testEndDateTime)
    {
        $this->setWithDefaultExpiration(self::testEndTimePrefix, $testEndDateTime);
    }

    /**
     * СРЕДНЕЕ КАЧЕСТВО ОТВЕТОВ СТУДЕНТА НА ВОПРОСЫ ТЕСТА
     */
    public function getAnswersQuality()
    {
        return $this->_redisClient->get(self::answersQualityPrefix.$this->sessionId);
    }

    public function setAnswersQuality($answersQuality)
    {
        $this->setWithDefaultExpiration(self::answersQualityPrefix, $answersQuality);
    }

    /**
     * ВОПРОСЫ ТЕСТА, НА КОТОРЫЕ УЖЕ БЫЛ ДАН ОТВЕТ
     */
    public function getAnsweredQuestionsIds()
    {
        $answeredQuestionsString =  $this->_redisClient->get(self::answeredQuestionsIdsPrefix.$this->sessionId);
        return $answeredQuestionsString == null ? [] : explode(',', $answeredQuestionsString);
    }

    public function setAnsweredQuestionsIds($answeredQuestionsIds)
    {
        $this->setWithDefaultExpiration(self::answeredQuestionsIdsPrefix,
            implode($answeredQuestionsIds, ','));
    }

    /**
     * ВСЕ ВОПРОСЫ ТЕСТА
     */
    public function getAllQuestionsIds()
    {
        $allQuestionsString =  $this->_redisClient->get(self::allQuestionsIdsPrefix.$this->sessionId);
        return $allQuestionsString == null ? [] : explode(',', $allQuestionsString);
    }

    public function setAllQuestionsIds($allQuestionsIds)
    {
        $this->setWithDefaultExpiration(self::allQuestionsIdsPrefix,
            implode($allQuestionsIds, ','));
    }

    /**
     * РЕЗУЛЬТАТ ТЕСТА
     */
    public function getTestResultId()
    {
        return $this->_redisClient->get(self::testResultPrefix.$this->sessionId);
    }

    public function setTestResultId($testResultId)
    {
        $this->setWithDefaultExpiration(self::testResultPrefix, $testResultId);
    }

    /**
     * КРАЙНЕЕ ВРЕМЯ ОТВЕТА НА ТЕКУЩИЙ ВОПРОС ТЕСТА
     */
    public function getQuestionEndTime()
    {
        return $this->_redisClient->get(self::questionEndTimePrefix.$this->sessionId);
    }

    public function setQuestionEndTime($questionEndTime)
    {
        $this->setWithDefaultExpiration(self::questionEndTimePrefix, $questionEndTime);
    }

    /**
     * Установка значения ключа в кэше с временем истечения по умолчанию.
     * @param prefix - Префикс ключа (без указания id сессии).
     * @param $value - Задаваемое значение.
     */
    private function setWithDefaultExpiration($prefix, $value){
        $testSessionCacheExpirationTime = TestSessionExpirationSettings::getInstance()->getTestSessionExpirationTime();

        $fullKey = $prefix.$this->sessionId;
        $this->_redisClient->set($fullKey, $value);
        $this->_redisClient->expireat($fullKey, strtotime($testSessionCacheExpirationTime));
    }

    /**
     * Добавление идентификатора вопроса в список идентификаторов отвеченных вопросов теста.
     * @param $questionId
     */
    public function addAnsweredQuestionId($questionId){
        $answeredQuestions = $this->getAnsweredQuestionsIds();
        array_push($answeredQuestions, $questionId);
        $this->setAnsweredQuestionsIds($answeredQuestions);
    }

    function jsonSerialize()
    {
        return ([
           'id' => $this->sessionId,
            'testId' => $this->getTestId(),
            'userId' => $this->getUserId(),
            'testResultId' => $this->getTestResultId(),
            'answeredIds' => $this->getAnsweredQuestionsIds(),
            'notAnsweredIds' => $this->getAllQuestionsIds(),
            'quality' => $this->getAnswersQuality(),
            'testEnd' => $this->getTestEndDateTime(),
            'questionEnd' => $this->getQuestionEndTime()
        ]);
    }
}