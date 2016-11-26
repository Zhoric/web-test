<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Managers\TestResultManager;
use TestEngine\TestResultCalculator;

class TestResultController extends Controller
{
    private $_testResultManager;

    public function __construct(TestResultManager $testResultManager)
    {
        $this->_testResultManager = $testResultManager;
    }

    /**
     * Получение последних результатов заданного теста для заданной группы.
     * @param Request $request
     * @return array
     */
    public function getByGroupAndTest(Request $request){
        try{
            $testId = $request->query('testId');
            $groupId = $request->query('groupId');

            $results = $this->_testResultManager->getByGroupAndTest($groupId, $testId);
            return $this->successJSONResponse($results);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /**
     * Получение результата теста вместе с ответами на вопросы по id
     * @param $id
     * @return \TestResultViewModel
     */
    public function getById($id){
        try{
            $result = $this->_testResultManager->getByIdWithAnswers($id);
            return $this->successJSONResponse($result);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getExtraAttemptsCount(Request $request){
        try{
            $testId = $request->query('testId');
            $userId = $request->query('userId');
            $count = $this->_testResultManager->getExtraAttemptsCount($userId, $testId);

            return $this->successJSONResponse($count);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }

    }

    /**
     * Установка дополнительных попыток для прохождения теста студентом.
     * @param Request $request
     * @return string
     */
    public function setExtraAttempts(Request $request){
        try{
            $testId = $request->json('testId');
            $userId = $request->json('userId');
            $count = $request->json('count');
            $this->_testResultManager->setExtraAttempts($userId, $testId, $count);

            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /**
     * Ручная установка оценки за вопрос теста с последующим пересчётом итоговой оценки за тест.
     * @param Request $request
     * @return string
     */
    public function setAnswerMark(Request $request){
        try{
            $givenAnswerId = $request->json('answerId');
            $mark = $request->json('mark');
            $resultMark = TestResultCalculator::setAnswerMark($givenAnswerId, $mark);
            return $this->successJSONResponse($resultMark);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /**
     * Получение результатов по заданному тесту и студенту.
     * @param Request $request
     * @return string
     */
    public function getByUserAndTest(Request $request){
        try{
            $testId = $request->query('testId');
            $userId = $request->query('userId');

            $results = $this->_testResultManager->getResultsByUserAndTest($userId, $testId);
            return $this->successJSONResponse($results);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}