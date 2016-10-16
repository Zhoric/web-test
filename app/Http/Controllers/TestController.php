<?php

namespace App\Http\Controllers;

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

        $test = new Test();
        $test->fillFromJson($testData);
        $this->_testManager->create($test, $themeIds);
    }

    public function update(Request $request){
        $testData = $request->json('test');
        $themeIds = $request->json('themeIds');

        $test = new Test();
        $test->fillFromJson($testData);
        $this->_testManager->update($test, $themeIds);
    }

    public function delete($id){
        $this->_testManager->delete($id);
    }
}
