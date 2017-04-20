<?php

namespace TestEngine;

use Exception;
use DateTime;
use ITestProcessStrategy;
use Managers\QuestionManager;
use Managers\SettingsManager;
use Managers\TestManager;
use Managers\TestResultManager;

class TestProcessTeachingStrategy extends BaseTestProcessStrategy implements ITestProcessStrategy
{
    public function __construct(TestManager $testManager,
                                TestResultManager $testResultManager,
                                QuestionManager $questionManager,
                                SettingsManager $settingsManager,
                                TestSessionFactory $testSessionFactory,
                                TestSessionTracker $testSessionTracker)
    {
        $this->_testManager = $testManager;
        $this->_testResultManager = $testResultManager;
        $this->_questionManager = $questionManager;
        $this->_settingsManager = $settingsManager;
        $this->_sessionFactory = $testSessionFactory;
        $this->_testSessionTracker = $testSessionTracker;
    }

    public function init($userId, $testId)
    {
        $this->validateAttemptNumber($userId, $testId);
        $session = $this->_sessionFactory->getInitialized($userId, $testId);
        $sessionId = $session->getSessionId();
        $this->_testSessionTracker->trackSession($sessionId);

        return $session->getSessionId();
    }

    public function getNextQuestion($sessionId)
    {
        $testSession = $this->_sessionFactory->getBySessionId($sessionId);
        $this->_testSession = $testSession;

        //Если были даны ответы на все вопросы, будет сформирован и возвращён результат.
        $this->checkIfNotAnsweredQuestionsExists();
        if ($this->_testResult != null) {
            return $this->_testResult;
        }

        $nextQuestionId = $this->getRandomQuestion($testSession);
        $question = $this->_testManager->getQuestionWithAnswers($nextQuestionId, false);

        $questionDuration = $question->getQuestion()->getTime();

        $questionEndTime = $date = new DateTime('+' . $questionDuration . ' seconds');
        $questionEndTime = $questionEndTime->format(GlobalTestSettings::dateSerializationFormat);

        $testSession->setQuestionEndTime($questionEndTime);
        $testSession->addAnsweredQuestionId($question->getQuestion()->getId());

        return $question;
    }

    public function processAnswer($sessionId, QuestionAnswer $questionAnswer)
    {
        try{
            $session = $this->_sessionFactory->getBySessionId($sessionId);
            $this->_testSession = $session;

            $questionId = $questionAnswer->getQuestionId();

            $this->validateQuestionToAnswer($questionId);

            $question = $this->_questionManager->getWithAnswers($questionId);

            $answerRightPercentage = $this->checkAnswers($question, $questionAnswer);
            $answerText = $this->getAnswerText($question, $questionAnswer);

            $this->saveQuestionAnswer($session, $questionId, $answerRightPercentage, $answerText);

        } catch (Exception $exception){
            $questionId = $questionAnswer->getQuestionId();
            $question = $this->_questionManager->getWithAnswers($questionId);
            $text = $this->getAnswerText($question, $questionAnswer);
            $this->saveQuestionAnswer($this->_testSession, $questionId, 0, $text);

            throw $exception;
        }
    }
}