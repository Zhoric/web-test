<?php

namespace TestEngine;

/**
 * Класс, отражающий состояние сессии тестирования.
 */
class TestSession
{
    /**
     * Идентификатор сессии.
     */
    private $sessionId;
    /**
     * Id пользователя, проходящего тест.
     */
    private $userId;

    /**
     * Id теста.
     */
    private $testId;

    /**
     * Время окончания теста.
     */
    private $testEndDateTime;

    /**
     * Текущее качество ответов тестируемого.
     * (будет использовано для адаптивного тестирования)
     */
    private $answersQuality;

    /**
     * Список id вопросов, на которые уже даны ответы.
     */
    private $answeredQuestionsIds;

    /**
     * Id записи с результатом теста в БД.
     */
    private $testResultId;

    /**
     * Срок истечения времени, отведённого на текущий вопрос.
     */
    private $questionEndTime;

    /**
     * @return mixed
     */
    public function getTestId()
    {
        return $this->testId;
    }

    /**
     * @param mixed $testId
     */
    public function setTestId($testId)
    {
        $this->testId = $testId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getTestEndDateTime()
    {
        return $this->testEndDateTime;
    }

    /**
     * @param mixed $testEndDateTime
     */
    public function setTestEndDateTime($testEndDateTime)
    {
        $this->testEndDateTime = $testEndDateTime;
    }

    /**
     * @return mixed
     */
    public function getAnswersQuality()
    {
        return $this->answersQuality;
    }

    /**
     * @param mixed $answersQuality
     */
    public function setAnswersQuality($answersQuality)
    {
        $this->answersQuality = $answersQuality;
    }

    /**
     * @return mixed
     */
    public function getAnsweredQuestionsIds()
    {
        return $this->answeredQuestionsIds;
    }

    /**
     * @param mixed $answeredQuestionsIds
     */
    public function setAnsweredQuestionsIds($answeredQuestionsIds)
    {
        $this->answeredQuestionsIds = $answeredQuestionsIds;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return mixed
     */
    public function getTestResultId()
    {
        return $this->testResultId;
    }

    /**
     * @param mixed $testResultId
     */
    public function setTestResultId($testResultId)
    {
        $this->testResultId = $testResultId;
    }

    /**
     * @return mixed
     */
    public function getQuestionEndTime()
    {
        return $this->questionEndTime;
    }

    /**
     * @param mixed $questionEndTime
     */
    public function setQuestionEndTime($questionEndTime)
    {
        $this->questionEndTime = $questionEndTime;
    }
}