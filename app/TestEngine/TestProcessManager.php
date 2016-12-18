<?php

namespace TestEngine;

/**
 * Класс, ответственный за управление процессом тестирования.
 */
class TestProcessManager
{
    private $_testProcessStrategyFactory;
    private $_testSessionFactory;

    public function __construct(TestProcessStrategyFactory $strategyFactory, TestSessionFactory $sessionFactory)
    {
        $this->_testProcessStrategyFactory = $strategyFactory;
        $this->_testSessionFactory = $sessionFactory;
    }

    public function initTest($userId, $testId)
    {
        $testProcessStrategy = $this->_testProcessStrategyFactory->getStrategy($testId);
        return $testProcessStrategy->init($userId, $testId);
    }

    public function getNextQuestion($sessionId)
    {
        $testId = $this->getTestId($sessionId);
        $testProcessStrategy = $this->_testProcessStrategyFactory->getStrategy($testId);
        return $testProcessStrategy->getNextQuestion($sessionId);
    }

    public function processAnswer($sessionId, QuestionAnswer $questionAnswer){
        $testId = $this->getTestId($sessionId);
        $testProcessStrategy = $this->_testProcessStrategyFactory->getStrategy($testId);
        return $testProcessStrategy->processAnswer($sessionId, $questionAnswer);
    }

    private function getTestId($sessionId){
        $session = $this->_testSessionFactory->getBySessionId($sessionId);
        return $session->getTestId();
    }
}