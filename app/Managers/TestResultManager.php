<?php

namespace Managers;

use DateTime;
use ExtraAttempt;
use League\Flysystem\Exception;
use Repositories\UnitOfWork;
use TestResult;
use TestResultViewModel;

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
            throw new Exception('Не удаётся начать тест. Указанного теста не существует!');
        }

        $testResult->setUser($user);
        $testResult->setTest($test);
        $testResult->setDateTime($now);
        $testResult->setAttempt($lastAttemptNumber+1);

        $this->_unitOfWork->testResults()->create($testResult);
        $this->_unitOfWork->commit();

        return $testResult->getId();
    }

    /**
     * Получение результата теста по id
     * @param $id
     * @return TestResult
     */
    public function getById($id){
        return $this->_unitOfWork->testResults()->find($id);
    }

    public function update(TestResult $testResult){
        $this->_unitOfWork->testResults()->update($testResult);
        $this->_unitOfWork->commit();

    }

    /**
     * Получение последних результатов заданного теста для заданной группы.
     * @param $testId
     * @param $groupId
     * @return array
     */
    public function getByGroupAndTest($groupId, $testId){
        return $this->_unitOfWork->testResults()->getByGroupAndTest($testId, $groupId);
    }

    /**
     * Получение результата теста со всеми ответами по id.
     * @param $testResultId
     * @return TestResultViewModel
     */
    public function getByIdWithAnswers($testResultId){
        $testResult = $this->_unitOfWork->testResults()->find($testResultId);
        $test = $testResult->getTest();
        $testId = $test->getId();
        $userId = $testResult->getUser()->getId();
        $answers =  $this->_unitOfWork->givenAnswers()->getByTestResult($testResultId);

        $extraAttempts = $this->_unitOfWork->extraAttempts()->findByTestAndUser($testId, $userId);
        $extraAttemptsCount = $extraAttempts != null ? $extraAttempts->getCount() : 0;
        $attemptsAllowedByDefault = $test->getAttempts();

        $totalAttemptsAllowed = $attemptsAllowedByDefault + $extraAttemptsCount;

        return new TestResultViewModel($testResult, $answers, $test, $totalAttemptsAllowed);
    }

    /**
     * Получение количества дополнительных попыток для прохождения теста студентом.
     * @param $userId
     * @param $testId
     * @return int
     */
    public function getExtraAttemptsCount($userId, $testId){
        $extraAttempts = $this->_unitOfWork
            ->extraAttempts()
            ->findByTestAndUser($userId, $testId);

        return $extraAttempts != null ? $extraAttempts->getCount() : 0;
    }

    /**
     * Установка количества дополнительных попыток для прохождения теста студентом.
     * @param $userId
     * @param $testId
     * @param $attemptsCount
     */
    public function setExtraAttempts($userId, $testId, $attemptsCount){
        $user = $this->_unitOfWork->users()->find($userId);
        $test = $this->_unitOfWork->tests()->find($testId);

        $existingExtraAttempts = $this->_unitOfWork
            ->extraAttempts()->findByTestAndUser($testId, $userId);

        if ($existingExtraAttempts != null){
            $existingExtraAttempts->setCount($attemptsCount);
            $this->_unitOfWork->extraAttempts()->update($existingExtraAttempts);
        } else {
            $attempts = new ExtraAttempt();
            $attempts->setTest($test);
            $attempts->setUser($user);
            $attempts->setCount($attemptsCount);

            $this->_unitOfWork->extraAttempts()->create($attempts);
        }

        $this->_unitOfWork->commit();
    }



}