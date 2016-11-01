<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Managers\TestResultManager;
use TestResult;


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
        $testId = $request->query('testId');
        $groupId = $request->query('groupId');
        return json_encode($this->_testResultManager->getByGroupAndTest($groupId, $testId));
    }

    /**
     * Получение результата теста вместе с отвами на вопросы по id
     * @param $id
     * @return \TestResultViewModel
     */
    public function getById($id){
        return json_encode($this->_testResultManager->getByIdWithAnswers($id));
    }

}