<?php

use TestEngine\TestProcessControlStrategy;
use TestEngine\TestProcessStrategyFactory;

class TestStrategyFactoryTests extends TestCase
{
    /**
     * Фабрика стратегий тестирования студентов должна возвращать стратегии в соответствии с типом теста.
     * (для обучающего теста - стратегию обучающего тестирования, для контроля знаний - стратегию контроля знаний).
     */
    public function testStrategyFactoryShouldReturnTestingStrategyForTestType(){
        //Arrange
        $testStub = new Test();
        $testStub->setId(1);
        $testStub->setType(TestType::Control);

        $testManagerStub = $this->createMock(\Managers\TestManager::class);
        $testManagerStub->method('getById')->with($this->equalTo(1))->willReturn($testStub);

        $testResultManagerStub = $this->createMock(\Managers\TestResultManager::class);
        $questionManagerStub = $this->createMock(\Managers\QuestionManager::class);
        $settingsManagerStub = $this->createMock(\Managers\SettingsManager::class);
        $sessionFactoryStub = $this->createMock(\TestEngine\TestSessionFactory::class);
        $sessionTrackerStub = $this->createMock(\TestEngine\TestSessionTracker::class);

        $testStrategyFactory = new TestProcessStrategyFactory($testManagerStub,
            $testResultManagerStub,
            $questionManagerStub,
            $settingsManagerStub,
            $sessionFactoryStub,
            $sessionTrackerStub);

        //Act
        $testProcessStrategy = $testStrategyFactory->getStrategy($testStub->getId());

        //Assert
        $this->assertTrue($testProcessStrategy instanceof TestProcessControlStrategy);
    }

    /**
     * Фабрика стратегий тестирования студентов должна выбрасывать исключение, если не указан тип теста.
     * @expectedException Exception
     * @expectedExceptionMessage Данный тип тестов не поддерживается
     */
    public function testStrategyFactoryShouldThrowExceptionIfTestTypeNotSpecified(){
        //Arrange
        $testStub = new Test();
        $testStub->setId(1);

        $testManagerStub = $this->createMock(\Managers\TestManager::class);
        $testManagerStub->method('getById')->with($this->equalTo(1))->willReturn($testStub);

        $testResultManagerStub = $this->createMock(\Managers\TestResultManager::class);
        $questionManagerStub = $this->createMock(\Managers\QuestionManager::class);
        $settingsManagerStub = $this->createMock(\Managers\SettingsManager::class);
        $sessionFactoryStub = $this->createMock(\TestEngine\TestSessionFactory::class);
        $sessionTrackerStub = $this->createMock(\TestEngine\TestSessionTracker::class);

        $testStrategyFactory = new TestProcessStrategyFactory($testManagerStub,
            $testResultManagerStub,
            $questionManagerStub,
            $settingsManagerStub,
            $sessionFactoryStub,
            $sessionTrackerStub);

        //Act & Assert
        $testProcessStrategy = $testStrategyFactory->getStrategy($testStub->getId());
    }


}