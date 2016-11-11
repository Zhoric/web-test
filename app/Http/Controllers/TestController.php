<?php

namespace App\Http\Controllers;

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
        $testData = $request->json('test');
        $themeIds = $request->json('themeIds');
        $disciplineId = $request->json('disciplineId');

        $test = new Test();
        $test->fillFromJson($testData);
        $this->_testManager->create($test, $themeIds, $disciplineId);
    }

    public function update(Request $request){
        $testData = $request->json('test');
        $themeIds = $request->json('themeIds');
        $disciplineId = $request->json('disciplineId');

        $test = new Test();
        $test->fillFromJson($testData);
        $this->_testManager->update($test, $themeIds, $disciplineId);
    }

    public function delete($id){
        $this->_testManager->delete($id);
    }

    public function getByNameAndDisciplinePaginated(Request $request){
        $pageNum =  $request->query('page');
        $pageSize = $request->query('pageSize');
        $disciplineId = $request->query('discipline');
        $name = $request->query('name');

        $paginationResult = $this->_testManager
            ->getTestsByNameAndDisciplinePaginated($pageNum, $pageSize, $name, $disciplineId);

        return json_encode($paginationResult);
    }

    /*
     * Получение тестов по конкретной дисциплине на главной странице для студента.
     */
    public function getStudentTestsByDiscipline(Request $requst){
        $currentUser = Auth::user();
        if (isset($currentUser)){
            $userId = $currentUser->getId();
            $disciplineId = $requst->query('discipline');
            $tests = $this->_testManager->getTestsByUserAndDiscipline($userId, $disciplineId);
            return json_encode($tests);
        } else {
            return json_encode(['message' => 'Невозможно получить данные о пользователе!']);
        }

    }

    public function getThemesOfTest($testId){
        $themes = $this->_testManager->getThemesOfTest($testId);
        return json_encode($themes);
    }


}
