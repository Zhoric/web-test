<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Managers\TestResultManager;
use TestEngine\TestResultCalculator;
use TestResultViewModel;
use GivenAnswerData;

class TestResultController extends Controller
{
    private $_testResultManager;

    public function __construct(TestResultManager $testResultManager)
    {
        $this->_testResultManager = $testResultManager;
    }

    /**
     * Получение последних результатов по группе, дисциплине, тесту
     * @param Request $request
     * @return array
     */
    public function getResults(Request $request){
        try{
            $testId = $request->query('testId');
            $groupId = $request->query('groupId');
            $disciplineId = $request->query('disciplineId');

            $results = $this->_testResultManager->getResults($groupId, $testId,$disciplineId);
            return $this->successJSONResponse($results);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getByDiscipline($disciplineId){
        try{
            $userId = $this->tryGetCurrentUserId();

            $results = $this->_testResultManager->getByUserAndDiscipline($userId, $disciplineId);
            return $this->successJSONResponse($results);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /**
     * Получение результата теста вместе с ответами на вопросы по id.
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

    /**
     * Получение результата теста вместе с ответами на вопросы по id (для студента).
     * @param $id
     * @return TestResultViewModel
     */
    public function getByIdForStudent($id){
        try{
            $studentId = $this->tryGetCurrentUserId();
            $result = $this->_testResultManager->getByIdWithAnswers($id, $studentId);
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
     *
     * Создает сущность студенческого ответа на вопрос (используется внешним модулем)
     * @param Request $request
     * @throws
     * @return int
     */
    public function createGivenAnswer(Request $request){

        try {
            $givenAnswerJson = $request->all();
            $contract = new GivenAnswerData();
            $contract->fillFromJson($givenAnswerJson);
            $givenAnswerId = $this->_testResultManager->createGivenAnswerEntity( $contract->getCode()
                                                               ,$contract->getTestResultId()
                                                               ,$contract->getQuestionId());

            return $givenAnswerId;
        }
        catch(Exception $e){
            throw new Exception("Не удалось создать ответ студента на вопрос. "
                . $e->getMessage());
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

    public function deleteOldResults(Request $request){
        try{
            $dateTime = $request->json('dateTime');

            $this->_testResultManager->deleteOlderThan($dateTime);

            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getGroupResults(Request $request){
        try{
            $groupId = $request->query('groupId');
            $disciplineId = $request->query('disciplineId');
            $startDate = $request->query('startDate');
            $endDate = $request->query('endDate');
            $selectionCriterion = $request->query('criterion');

            $results = $this->_testResultManager
                ->getGroupTestPassingChronology($groupId, $disciplineId, $startDate, $endDate, $selectionCriterion);

            return $this->successJSONResponse(array_values($results));
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}