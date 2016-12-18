<?php

namespace Managers;

use DateTime;
use ExtraAttempt;
use Helpers\DateHelper;
use League\Flysystem\Exception;
use Repositories\UnitOfWork;
use Test;
use TestResult;
use TestResultViewModel;
use TestType;
use UserRole;

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
        $lastAttemptNumber = $this->_unitOfWork
            ->testResults()
            ->getLastAttemptNumber($testId,$userId);
        $now = DateHelper::getCurrentDateTime();
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
     * Получение последних результатов по заданным дисциплине и студенту.
     * @param $userId
     * @param $disciplineId
     * @return array
     */
    public function getByUserAndDiscipline($userId, $disciplineId){
        return $this->_unitOfWork->testResults()->getByUserAndDiscipline($userId, $disciplineId);
    }

    /**
     * Получение результата теста со всеми ответами по id.
     * @param $testResultId
     * @param null $studentId - Id пользователя. Заполняется, если результат запрашивается
     * пользователем с ролью "Студент". В этом случае необходима дополнительная логика отображения
     * развёрнутой информации о прохождении теста.
     * @return TestResultViewModel
     * @throws \Exception
     */
    public function getByIdWithAnswers($testResultId, $studentId = null){
        $testResult = $this->_unitOfWork->testResults()->find($testResultId);
        if (!isset($testResult)){
            throw new \Exception('Запрошенный результат не найден!');
        }

        $test = $testResult->getTest();
        $testId = $test->getId();
        $userId = $testResult->getUser()->getId();
        $answers =  $this->_unitOfWork->givenAnswers()->getByTestResult($testResultId);

        $extraAttempts = $this->_unitOfWork->extraAttempts()->findByTestAndUser($testId, $userId);
        $extraAttemptsCount = $extraAttempts != null ? $extraAttempts->getCount() : 0;
        $attemptsAllowedByDefault = $test->getAttempts();

        $totalAttemptsAllowed = $attemptsAllowedByDefault + $extraAttemptsCount;

        $testResultViewModel = new TestResultViewModel($testResult, $answers, $test, $totalAttemptsAllowed);

        if (isset($studentId)){
            $testResultViewModel = $this->prepareForStudentResultRequest($test, $testResultViewModel, $studentId);
        }

        return $testResultViewModel;
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

    public function getResultsByUserAndTest($userId, $testId){
        $testResults = $this
            ->_unitOfWork
            ->testResults()
            ->getByUserAndTest($userId, $testId);

        return $testResults;
    }

    /**
     * Обработка данных о результате прохождения теста перед отправкой студенту.
     * Результат прохождения теста будет показан студенту в развёрнутой форме только если тип теста - обучающий.
     * В противном случае студент получит лишь общий результат.
     * Также выполняется проверка на то, что пользователь запрашивает свой результат, а не чей-либо ещё.
     * @param Test $test
     * @param TestResultViewModel $resultViewModel
     * @param $studentId
     * @return TestResultViewModel
     * @throws Exception
     */
    private function prepareForStudentResultRequest(Test $test, TestResultViewModel $resultViewModel, $studentId){

        $requestedTestResultStudentId = $resultViewModel->getTestResult()->getUser()->getId();

        if ($requestedTestResultStudentId != $studentId){
            throw new Exception('Ошибка! Доступ к результатам других студентов запрещён!');
        }

        if ($test->getType() === TestType::Control){
            $resultViewModel->setAnswers(null);
        }

        return $resultViewModel;
    }

    public function delete($testResultId){
        $testResult = $this->_unitOfWork->testResults()->find($testResultId);
        if (!isset($testResult)){
            throw new Exception('Ошибка! Указанный результат теста не найден!');
        }
        $this->_unitOfWork->testResults()->delete($testResult);
        $this->_unitOfWork->commit();
    }

}