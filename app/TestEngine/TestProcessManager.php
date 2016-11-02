<?php

namespace TestEngine;
use Exception;
use DateTime;
use GivenAnswer;
use Illuminate\Session\SessionManager;
use Managers\QuestionManager;
use Managers\TestManager;
use Managers\TestResultManager;
use Question;
use QuestionType;
use QuestionViewModel;
use TestResult;

/**
 * Класс, ответственный за управление процессом тестирования.
 */
class TestProcessManager
{
    const dateFormat = 'Y-m-d H:i:s';

    /**
     * @var TestManager
     */
    private static $_testManager;

    /**
     * @var TestResultManager
     */
    private static $_testResultManager;

    /**
     * @var QuestionManager
     */
    private static $_questionManager;

    /**
     * @var TestSession
     */
    private static $_session;

    /**
     * @var TestResult
     */
    private static $_testResult;

    /**
     * @return QuestionManager
     */
    private static function getQuestionManager(){
        if (self::$_questionManager == null){
            self::$_questionManager = app()->make(QuestionManager::class);
        }

        return self::$_questionManager;
    }

    /**
     * @return TestManager
     */
    private static function getTestManager(){
        if (self::$_testManager == null){
            self::$_testManager = app()->make(TestManager::class);
        }

        return self::$_testManager;
    }

    /**
     * @return TestResultManager
     */
    private static function getTestResultManager(){
        if (self::$_testResultManager == null){
            self::$_testResultManager = app()->make(TestResultManager::class);
        }

        return self::$_testResultManager;
    }
    /**
     * Инициализация процесса тестирования.
     */
    public static function initTest($userId, $testId)
    {
        //DEBUG COMMENT
        //self::validateAttemptNumber($userId, $testId);

        $sessionId = TestSessionHandler::createTestSession($testId, $userId);
        return $sessionId;
    }

    /**
     * Получение следующего вопроса на основании настроек теста
     * и списка вопросов, на которые уже были даны ответы.
     */
    public static function getNextQuestion($sessionId){
        try{
            self::$_testManager = TestSessionHandler::getTestManager();
            self::$_session = TestSessionHandler::getSession($sessionId);

            self::validateTestSession();

            $suitableQuestions = self::getSuitableQuestionsIds();
            self::validateSuitableQuestions($suitableQuestions);

            if (self::$_testResult != null){
                return self::$_testResult;
            }

            $nextQuestionId = self::getRandomQuestion($suitableQuestions);

        } catch (Exception $exception){
            return array('message' => $exception->getMessage());
        }

        return self::$_testManager->getQuestionWithAnswers($nextQuestionId, false);
    }

    /**
     * Обработка ответа студента на вопрос теста.
     * @param $sessionId - Идентификатор сессии.
     * @param QuestionAnswer $questionAnswer - Ответ на вопрос теста.
     * @return array
     */
    public static function processAnswer($sessionId, QuestionAnswer $questionAnswer){
        try{
            $session = TestSessionHandler::getSession($sessionId);
            self::$_session = $session;
            self::validateTestSession();

            $questionId = $questionAnswer->getQuestionId();

            //DEBUG COMMENT
            //self::validateQuestionToAnswer($questionId);

            $question = self::getQuestionManager()->getWithAnswers($questionId);

            $answerRightPercentage = self::checkAnswers($question, $questionAnswer);
            $answerText = self::getAnswerText($question, $questionAnswer);

            self::saveQuestionAnswer($session, $questionId, $answerRightPercentage, $answerText);
            self::updateTestSession($questionId);

        } catch (Exception $exception){
            return array('message' => 'Ошибка при обработке ответа: '.$exception->getMessage());
        }
    }

    /**
     * Обработка окончания теста. Подсчёт и сохранение результатов.
     */
    public static function processTestEnd(){
        $testResultId = self::$_session->getTestResultId();
        self::calculateAnsSaveResult($testResultId);
    }

    /**
     * Подсчёт и сохранение оценки за тест.
     * @param $testResultId
     */
    public static function calculateAnsSaveResult($testResultId){
        $testResultMark = TestResultCalculator::calculate($testResultId);
        $now = new DateTime();

        $testResult = self::getTestResultManager()->getById($testResultId);
        $testResult->setMark($testResultMark);
        $testResult->setDateTime($now);

        self::$_testResult = $testResult;
        self::getTestResultManager()->update($testResult);
        self::$_testResult->setDateTime($now->format(self::dateFormat));

    }

    /**
     * Получение идентификаторов вопросов, подходящих под условия,
     * настройки и текущее состояние процесса тестирования.
     */
    private static function getSuitableQuestionsIds(){
        $testManager = self::$_testManager;
        $session = self::$_session;

        $testId = $session->getTestId();
        $test = $testManager->getById($testId);
        $answeredQuestionsIds = $session->getAnsweredQuestionsIds();

        $timeLeft = self::getTimeLeftBeforeTestEnd();
        $suitableQuestionsIds = $testManager->getNotAnsweredQuestionsByTest(
            $testId,
            $answeredQuestionsIds,
            $timeLeft);

        return $suitableQuestionsIds;
    }

    /**
     * Подсчёт количества секунд, оставшихся до конца тестирования.
     */
    private static function getTimeLeftBeforeTestEnd(){
        $now = date(self::dateFormat);
        $testTimeLeft = self::$_session->getTestEndDateTime()->format(self::dateFormat);

        $secondsLeft = strtotime($testTimeLeft) - strtotime($now);
        return $secondsLeft;
    }

    /**
     * Выбор случайного вопроса из списка подходящих.
     */
    private static function getRandomQuestion($suitableQuestions){
        array_flatten($suitableQuestions);
        $nextQuestionIndex = array_rand($suitableQuestions);
        $nextQuestionId = $suitableQuestions[$nextQuestionIndex]['id'];

        return $nextQuestionId;
    }

    /**
     * Обновление состояния сессии тестирования.
     * @param $answeredQuestionId - id вопроса, на который был дан ответ.
     */
    private static function updateTestSession($answeredQuestionId){
        $answeredQuestionsIds = self::$_session->getAnsweredQuestionsIds();
        array_push($answeredQuestionsIds, $answeredQuestionId);
        $sessionId = self::$_session->getSessionId();

        TestSessionHandler::setSessionAnsweredQuestions($sessionId, $answeredQuestionsIds);
    }

    /**
     * Валидация сессии тестирования.
     */
    private static function validateTestSession(){
        $endTime = self::$_session->getTestEndDateTime();
        if ($endTime == null || $endTime == ''){
            throw new Exception('Не найдена сессия тестирования!');
        }

        $timeLeft = self::getTimeLeftBeforeTestEnd();
        if (GlobalTestSettings::testEndTolerance + $timeLeft <= 0){
            throw new Exception('Время, отведённое на тест истекло!');
        }
    }

    /**
     * Валидация списка подходящих вопросов.
     * В случае отсутствия подходящих вопросов, тест завершается.
     */
    private static function validateSuitableQuestions($suitableQuestionsIds){

        if ($suitableQuestionsIds == null || empty($suitableQuestionsIds)){
            self::processTestEnd();
        }
    }

    /**
     * Проверка на то, что мы дважды не отвечаем на один и тот же вопрос
     * @param $questionId - id вопроса, на который даётся ответ.
     * @throws Exception
     */
    private static function validateQuestionToAnswer($questionId){
        $answeredQuestionsIds = self::$_session->getAnsweredQuestionsIds();

        if (in_array($questionId, $answeredQuestionsIds)){
            throw new Exception('Вы уже отвечали на этот вопрос!');
        }
    }

    /**
     * Проверка номера попытки прохождения теста.
     * @param $userId
     * @param $testId
     * @throws Exception
     */
    private static function validateAttemptNumber($userId, $testId){
        $test = self::getTestManager()->getById($testId);

        $attemptsAllowed = $test->getAttempts();

        $extraAttempts = self::$_testResultManager->getExtraAttemptsCount($userId, $testId);
        $lastAttempt = self::getTestManager()->getTestAttemptsUsedCount($userId, $testId);

        if ($attemptsAllowed != 0 && $lastAttempt >= $attemptsAllowed + $extraAttempts){
            throw new Exception('Количество попыток прохождения теста исчерпано!');
        }
    }

    /**
     * Проверка правильности ответов
     * @param QuestionViewModel $questionInfo - Вопрос со всеми его ответами.
     * @param QuestionAnswer $questionAnswer - ответы, которые дал студент
     * @return int - оценка за ответ, %
     * @throws Exception
     */
    private static function checkAnswers($questionInfo, $questionAnswer){
        $answerResultPoints = null;

        $question = $questionInfo->getQuestion();
        $questionType = $question->getType();
        $answers = $questionInfo->getAnswers();

        switch ($questionType){
            case QuestionType::ClosedOneAnswer:
            case QuestionType::ClosedManyAnswers: {
                $studentAnswers = $questionAnswer->getAnswerIds();
                $answerResultPoints =  AnswerChecker::calculatePointsForClosedAnswer($answers, $studentAnswers);
                break;
            }
            case QuestionType::OpenOneString:{
                $answerText = $questionAnswer->getAnswerText();
                $answerResultPoints = AnswerChecker::calculatePointsForSingleStringAnswer($answers, $answerText);
                break;
            }
        }

        return $answerResultPoints;
    }

    /**
     * Сохранение ответа студента в БД.
     * @param TestSession $session - сессия тестирования.
     * @param $questionId - вопрос, на который дан ответ.
     * @param $rightPercentage - степень правильности ответа.
     * @param string $answerText - текст ответа.
     * @throws \Exception
     */
    private static function saveQuestionAnswer($session, $questionId, $rightPercentage, $answerText = ''){
        $testResultId = $session->getTestResultId();
        $testResult = TestSessionHandler::getTestResultManager()->getById($testResultId);
        $question = self::getQuestionManager()->getById($questionId);

        if ($testResultId == null || $question == null){
            throw new Exception('Не удалось сохранить ответ!');
        }

        $givenAnswer = new GivenAnswer();
        $givenAnswer->setTestResult($testResult);
        $givenAnswer->setQuestion($question);
        $givenAnswer->setRightPercentage($rightPercentage);
        $givenAnswer->setAnswer($answerText);

        self::getQuestionManager()->createQuestionAnswer($givenAnswer);
    }

    /**
     * Получение текста ответа на вопрос.
     * В случае, если вопрос закрытый - в текст ответа копируется текст всех выбранных вариантов.
     * Если вопрос открытый, то в текст ответа будет сохранён текст, введённый студентом при ответе.
     * @param QuestionViewModel $questionAnswer - вопрос со всеми возможными ответами.
     * @param QuestionAnswer $studentAnswer - ответ студента
     * @return string
     * @internal param Question $question
     */
    private static function getAnswerText($questionAnswer, $studentAnswer){
        $openAnswerText = $studentAnswer->getAnswerText();
        if ($openAnswerText != null && $openAnswerText != ""){
            return $openAnswerText;
        }

        $studentAnswersIds = $studentAnswer->getAnswerIds();
        $questionType = $questionAnswer->getQuestion()->getType();
        $answers = $questionAnswer->getAnswers();
        $answerText = '';

        switch ($questionType){
            case QuestionType::ClosedOneAnswer: {
                foreach ($answers as $answer){
                    if (in_array($answer->getId(), $studentAnswersIds)){
                        $answerText .= $answer->getText().'; ';
                    }
                }
                break;
            }
            case QuestionType::ClosedManyAnswers: {
                foreach ($answers as $answer){
                    if (in_array($answer->getId(), $studentAnswersIds)){
                        $answerText .= $answer->getText().'; ';
                    }
                }
                break;
            }
        }
        return $answerText;
    }


}