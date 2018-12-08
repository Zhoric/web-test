<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Managers\TestManager;
use Test;
use Illuminate\Http\Request;
use Managers\LecturerManager;


class TestController extends Controller
{
    private $_testManager;

    public function __construct(TestManager $testManager)
    {
        $this->_testManager = $testManager;
    }

    public function create(Request $request){
        try{
            $testData = $request->json('test');
            $themeIds = $request->json('themeIds');
            $disciplineId = $request->json('disciplineId');

            $test = new Test();
            $test->fillFromJson($testData);
            $this->_testManager->create($test, $themeIds, $disciplineId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function update(Request $request){
        try{
            $testData = $request->json('test');
            $themeIds = $request->json('themeIds');
            $disciplineId = $request->json('disciplineId');

            $test = new Test();
            $test->fillFromJson($testData);
            $this->_testManager->update($test, $themeIds, $disciplineId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($id){
        try{
            $this->_testManager->delete($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getByNameAndDisciplinePaginated(Request $request){
        try{
            $pageNum =  $request->query('page');
            $pageSize = $request->query('pageSize');
            $disciplineId = $request->query('discipline');
            $name = $request->query('name');
            $isActive = $request->query('isActive');

            $paginationResult = $this->_testManager
                ->getTestsByNameAndDisciplinePaginated($pageNum, $pageSize, $name, $disciplineId, $isActive);

            return $this->successJSONResponse($paginationResult);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /*
     * Получение тестов по конкретной дисциплине на главной странице для студента.
     */
    public function getStudentTestsByDiscipline(Request $requst){
        try{
            $currentUser = Auth::user();
            if (isset($currentUser)){
                $userId = $currentUser->getId();
                $disciplineId = $requst->query('discipline');
                $tests = $this->_testManager->getTestsByUserAndDiscipline($userId, $disciplineId);

                return $this->successJSONResponse($tests);
            } else {
                throw new Exception('Невозможно получить данные о пользователе!');
            }
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getThemesOfTest($testId){
        try{
            $themes = $this->_testManager->getThemesOfTest($testId);
            return $this->successJSONResponse($themes);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }


}
