<?php

namespace TestEngine;
use DateTimeZone;
use Exception;
use DateTime;
use GivenAnswer;
use Illuminate\Session\SessionManager;
use Managers\QuestionManager;
use Managers\SettingsManager;
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
     * @var SettingsManager
     */
    private static $_settingsManager;

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
     * @return SettingsManager
     */
    private static function getSettingsManager(){
        if (self::$_settingsManager == null){
            self::$_settingsManager = app()->make(SettingsManager::class);
        }
        return self::$_settingsManager;
    }

    /**
     * Инициализация процесса тестирования.
     */
    public static function initTest($userId, $testId)
    {
        self::validateAttemptNumber($userId, $testId);

        $sessionId = TestSessionHandler::createTestSession($testId, $userId);
        return $sessionId;
    }

    /**
     * Получение следующего вопроса на основании настроек теста
     * и списка вопросов, на которые уже были даны ответы.
     */
    public static function getNextQuestion($sessionId){
            self::$_testManager = TestSessionHandler::getTestManager();
            self::$_session = TestSessionHandler::getSession($sessionId);

            self::validateTestSession();

            $suitableQuestions = self::getSuitableQuestionsIds();
            self::validateSuitableQuestions($suitableQuestions);

            if (self::$_testResult != null){
                return self::$_testResult;
            }

            $nextQuestionId = self::getRandomQuestion($suitableQuestions);
            $question = self::getQuestionManager()->getById($nextQuestionId);

            TestSessionHandler::setSessionQuestionEndTime(
                self::$_session->getSessionId(),
                $question->getTime());

            self::updateTestSession($nextQuestionId);

        return self::$_testManager->getQuestionWithAnswers($nextQuestionId, false);
    }

    /**
     * Обработка ответа студента на вопрос теста.
     * @param $sessionId - Идентификатор сессии.
     * @param QuestionAnswer $questionAnswer - Ответ на вопрос теста.
     * @return array
     * @throws Exception
     */
    public static function processAnswer($sessionId, QuestionAnswer $questionAnswer){
        try{
            $session = TestSessionHandler::getSession($sessionId);
            self::$_session = $session;

            $questionId = $questionAnswer->getQuestionId();

            self::validateTestSession($questionId);
            self::validateQuestionToAnswer($questionId);

            $question = self::getQuestionManager()->getWithAnswers($questionId);

            $answerRightPercentage = self::checkAnswers($question, $questionAnswer);
            $answerText = self::getAnswerText($question, $questionAnswer);

            self::saveQuestionAnswer($session, $questionId, $answerRightPercentage, $answerText);

        } catch (Exception $exception){
            $questionId = $questionAnswer->getQuestionId();
            $question = self::getQuestionManager()->getWithAnswers($questionId);
            $text = self::getAnswerText($question, $questionAnswer);
            self::saveQuestionAnswer(self::$_session, $questionId, 0, $text);

            throw $exception;
        }
    }

    /**
     * Обработка окончания теста. Подсчёт и сохранение результатов.
     */
    public static function processTestEnd(){
        $testResultId = self::$_session->getTestResultId();
        self::calculateAndSaveResult($testResultId);
    }

    /**
     * Подсчёт и сохранение оценки за тест.
     * @param $testResultId
     */
    public static function calculateAndSaveResult($testResultId){

        $testResultMark = TestResultCalculator::calculate($testResultId);
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Moscow'));

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
     * Подсчёт количества секунд, оставшихся на ответ на текущий вопрос.
     */
    private static function getTimeLeftBeforeQuestionEnd(){
        $now = date(self::dateFormat);
        $questionTimeLeft = self::$_session->getQuestionEndTime()->format(self::dateFormat);

        $secondsLeft = strtotime($questionTimeLeft) - strtotime($now);
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
     * Проверка истечения времени на текущий вопрос и на тест в целом.
     * @param null $questionId
     * @throws Exception
     */
    private static function validateTestSession($questionId = null){
        $endTime = self::$_session->getTestEndDateTime();
        if ($endTime == null || $endTime == ''){
            throw new Exception('Не найдена сессия тестирования!');
        }

        $timeLeftToEnd = self::getTimeLeftBeforeTestEnd();
        $testEndTolerance = self::getSettingsManager()->get(GlobalTestSettings::testEndToleranceKey);
        $questionEndTolerance = self::getSettingsManager()->get(GlobalTestSettings::questionEndToleranceKey);

        if ($testEndTolerance + $timeLeftToEnd <= 0){
            throw new Exception('Время, отведённое на тест истекло!');
        }
        if ($questionId != null){
            $timeLeftToQuestion = self::getTimeLeftBeforeQuestionEnd();
            if ($questionEndTolerance + $timeLeftToQuestion <= 0){
                self::saveQuestionAnswer(self::$_session, $questionId, 0);
                throw new Exception('Время, отведённое на данный вопрос истекло. Ответ не будет засчитан!');
            }
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

        if (in_array($questionId, $answeredQuestionsIds) &&
                $questionId != $answeredQuestionsIds[count($answeredQuestionsIds) - 1]){
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

        $extraAttempts = self::getTestResultManager()->getExtraAttemptsCount($userId, $testId);
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
            case QuestionType::WithProgram:{
                $studentCode = $questionAnswer->getAnswerText();
                $questionId = $question->getId();
                $answerResultPoints = AnswerChecker::calculatePointsForProgramAnswer($questionId, $studentCode);
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
                $answerText = $answers[0]->getText();
                break;
            }
            case QuestionType::ClosedManyAnswers: {
                foreach ($answers as $answer){
                    if (in_array($answer->getId(), $studentAnswersIds)){
                        $answerText .= $answer->getText().'</answer>';
                    }
                }
                break;
            }
        }
        return $answerText;
    }

}