<?php

namespace TestEngine;

use Exception;
use Managers\QuestionManager;
use Managers\SettingsManager;
use Managers\TestManager;
use Managers\TestResultManager;
use TestType;

/**
 * Фабрика стратегий процесса тестирования.
 * Class TestProcessStrategyFactory
 * @package TestEngine
 */
class TestProcessStrategyFactory
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

    public function __construct(TestManager $testManager,
                                TestResultManager $testResultManager,
                                QuestionManager $questionManager,
                                SettingsManager $settingsManager,
                                TestSessionFactory $testSessionFactory)
    {
        $this->_testManager = $testManager;
        $this->_testResultManager = $testResultManager;
        $this->_questionManager = $questionManager;
        $this->_settingsManager = $settingsManager;
        $this->_sessionFactory = $testSessionFactory;
    }

    /**
     * Метод возвращает стратегию процесса тестирования в соответствии с типом теста.
     * @param $testId
     * @return TestProcessControlStrategy
     * @throws Exception
     */
    public function getStrategy($testId){
        $testType = $this->tryGetTestType($testId);

        switch ($testType){
            case TestType::Control: {
                return new TestProcessControlStrategy(
                    $this->_testManager,
                    $this->_testResultManager,
                    $this->_questionManager,
                    $this->_settingsManager,
                    $this->_sessionFactory);
            }
            case TestType::Teaching: {

            }
            default:{
                throw new Exception('Данный тип тестов не поддерживается!');
            }
        }
    }

    /**
     * Получение типа указанного теста.
     * @param $testId
     * @return int
     * @throws Exception
     */
    private function tryGetTestType($testId){

        $test = $this->_testManager->getById($testId);
        if (!isset($test)){
            throw new Exception('Ошибка! Указанный тест не найден!');
        }

        return $test->getType();
    }

}