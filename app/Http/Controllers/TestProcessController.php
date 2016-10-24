<?php

namespace App\Http\Controllers;

use Exception;
use Managers\TestManager;
use Test;
use Illuminate\Http\Request;
use Managers\LecturerManager;
use TestEngine\QuestionAnswer;
use TestEngine\TestProcessManager;


class TestProcessController extends Controller
{
    public function startTest(Request $request){
        $result = null;
        try{
            $testId = $request->json('testId');
            //TODO: Получать id текущего пользователя
            $userId = 5;

            $result = TestProcessManager::initTest($userId, $testId);
            $request->session()->set('sessionId', $result);
        } catch (Exception $exception){
            return json_encode(array('message' => $exception->getMessage()));
        }

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
