<?php

namespace Managers;

use DateTime;
use League\Flysystem\Exception;
use Repositories\UnitOfWork;
use TestResult;

class TestResultManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    /**
     * Создание пустого результата теста для последующей привязки
     * ответов к нему.
     * Поле "оценка" будет заполнено по окончанию теста на основании правильности ответов.
     * Функция возвращает id созданного результата для сохранения его в сессии
     */
    public function createEmptyTestResult($userId, $testId){
        $testResult = new TestResult();
        $user = $this->_unitOfWork->users()->find($userId);
        $test = $this->_unitOfWork->tests()->find($testId);
        $now = new DateTime();
        $lastAttemptNumber = $this->_unitOfWork
            ->testResults()
            ->getLastAttemptNumber($testId,$userId);

        if ($user == null){
            throw new Exception('Не удаётся начать тест. Указанного пользователя не существует!');
        }
        if ($test == null){
            throw new Exception('Не уrдаётся начать тест. Указанного теста не существует!');
        }

        $testResult->setUser($user);
        $testResult->setTest($test);
        $testResult->setDateTime($now);
        $testResult->setAttempt($lastAttemptNumber+1);

        $this->_unitOfWork->testResults()->create($testResult);
        $this->_unitOfWork->commit();

        return $testResult->getId();
    }
}