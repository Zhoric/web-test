<?php

namespace App\Http\Controllers;

use Managers\TestManager;
use Test;
use Illuminate\Http\Request;
use Managers\LecturerManager;
use TestEngine\QuestionAnswer;
use TestEngine\TestProcessManager;


class TestProcessController extends Controller
{
    public function startTest(Request $request){
        $testId = $request->json('testId');
        //TODO: Получать id текущего пользователя
        $userId = 5;

        $sessionId = TestProcessManager::initTest($userId, $testId);
        $request->session()->set('sessionId', $sessionId);
    }

    public function getNextQuestion(Request $request){
        $sessionId = $request->session()->get('sessionId');
        $nextQuestionRequestResult = TestProcessManager::getNextQuestion($sessionId);

        return json_encode($nextQuestionRequestResult);
    }

    public function answer(Request $request){
        $sessionId = $request->session()->get('sessionId');
        $questionId = $request->json('questionId');
        $answersIds = $request->json('answersIds');
        $answerText = $request->json('answerText');

        $questionAnswer = new QuestionAnswer();
        $questionAnswer->setQuestionId($questionId);
        $questionAnswer->setAnswerIds($answersIds);
        $questionAnswer->setAnswerText($answerText);

        $result = TestProcessManager::processAnswer($sessionId, $questionAnswer);

        return json_encode($result);
    }
}
