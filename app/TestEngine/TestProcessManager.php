<?php

namespace TestEngine;
use DateTimeZone;
use Exception;
use DateTime;
use GivenAnswer;
use Helpers\DateHelper;
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
    /**
     * @var TestManager
     */
    private $_testManager;

    /**
     * @var TestResultManager
     */
    private $_testResultManager;

    /**
     * @var QuestionManager
     */
    private $_questionManager;

    /**
     * @var SettingsManager
     */
    private $_settingsManager;

    /**
     * @var TestSessionFactory
     */
    private $_sessionFactory;

    /**
     * @var TestSession
     */
    private $_testSession;

    /**
     * @var TestResult
     */
    private $_testResult;

    public function __construct(TestManager $testManager,
                                TestResultManager $testResultManager,
                                QuestionManager $questionManager,
                                SettingsManager $settingsManager,
                                TestSessionFactory $testSessionFactory)
    {
        $this->_testManager = $testManager;
        $this->_testResultManager = $testResultManager;
        $this->_questionManager = $questionManager;
        $this->_settingsManager = $settingsManager;
        $this->_sessionFactory = $testSessionFactory;
    }

    /**
     * Инициализация процесса тестирования.
     */
    public function initTest($userId, $testId)
    {
        $this->validateAttemptNumber($userId, $testId);
        $session = $this->_sessionFactory->getInitialized($userId, $testId);
        return $session->getSessionId();
    }

    /**
     * Получение следующего вопроса на основании настроек теста
     * и списка вопросов, на которые уже были даны ответы.
     */
    public function getNextQuestion($sessionId)
    {
        $testSession = $this->_sessionFactory->getBySessionId($sessionId);
        $this->_testSession = $testSession;
        $this->validateTestSession();

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

    /**
     * Обработка ответа студента на вопрос теста.
     * @param $sessionId - Идентификатор сессии.
     * @param QuestionAnswer $questionAnswer - Ответ на вопрос теста.
     * @return array
     * @throws Exception
     */
    public function processAnswer($sessionId, QuestionAnswer $questionAnswer){
        try{
            $session = $this->_sessionFactory->getBySessionId($sessionId);
            $this->_testSession = $session;

            $questionId = $questionAnswer->getQuestionId();

            $this->validateTestSession($questionId);
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

    /**
     * Обработка окончания теста. Подсчёт и сохранение результатов.
     */
    public function processTestEnd(){
        $testResultId = $this->_testSession->getTestResultId();
        $this->calculateAndSaveResult($testResultId);
    }

    /**
     * Подсчёт и сохранение оценки за тест.
     * @param $testResultId
     */
    public function calculateAndSaveResult($testResultId){

        $testResultMark = TestResultCalculator::calculate($testResultId);

        $testResult = $this->_testResultManager->getById($testResultId);
        $testResult->setMark($testResultMark);

        $this->_testResult = $testResult;
        $this->_testResultManager->update($testResult);
    }

    /**
     * Подсчёт количества секунд, оставшихся до конца тестирования.
     */
    private function getTimeLeftBeforeTestEnd(){
        $now = date(GlobalTestSettings::dateSerializationFormat);
        $testTimeLeft = $this->_testSession->getTestEndDateTime();

        $secondsLeft = strtotime($testTimeLeft) - strtotime($now);
        return $secondsLeft;
    }

    /**
     * Подсчёт количества секунд, оставшихся на ответ на текущий вопрос.
     */
    private function getTimeLeftBeforeQuestionEnd(){
        $now = date(GlobalTestSettings::dateSerializationFormat);
        $questionTimeLeft = $this->_testSession->getQuestionEndTime();

        $secondsLeft = strtotime($questionTimeLeft) - strtotime($now);
        return $secondsLeft;
    }

    /**
     * Выбор случайного вопроса из списка подходящих.
     */
    private function getRandomQuestion(TestSession $testSession){
        $answeredQuestions = $testSession->getAnsweredQuestionsIds();
        $allQuestions = $testSession->getAllQuestionsIds();

        $notAnsweredQuestions = array_diff($allQuestions, $answeredQuestions);

        array_flatten($notAnsweredQuestions);
        $nextQuestionIndex = array_rand($notAnsweredQuestions);
        $nextQuestionId = $notAnsweredQuestions[$nextQuestionIndex];

        return $nextQuestionId;
    }

    /**
     * Валидация сессии тестирования.
     * Проверка истечения времени на текущий вопрос и на тест в целом.
     * @param null $questionId
     * @throws Exception
     */
    private function validateTestSession($questionId = null){
        $endTime = $this->_testSession->getTestEndDateTime();
        if ($endTime == null || $endTime == ''){
            throw new Exception('Не найдена сессия тестирования!');
        }

        $timeLeftToEnd = $this->getTimeLeftBeforeTestEnd();
        $testEndTolerance = $this->_settingsManager->get(GlobalTestSettings::testEndToleranceKey);
        $questionEndTolerance = $this->_settingsManager->get(GlobalTestSettings::questionEndToleranceKey);

        if ($testEndTolerance + $timeLeftToEnd <= 0){
            $this->processTestEnd();
            throw new Exception('Время, отведённое на тест истекло!');
        }
        if ($questionId != null){
            $timeLeftToQuestion = $this->getTimeLeftBeforeQuestionEnd();
            if ($questionEndTolerance + $timeLeftToQuestion <= 0){
                $this->saveQuestionAnswer($this->_testSession, $questionId, 0);
                throw new Exception('Время, отведённое на данный вопрос истекло. Ответ не будет засчитан!');
            }
        }
    }

    /**
     * Проверка на то, что мы дважды не отвечаем на один и тот же вопрос
     * @param $questionId - id вопроса, на который даётся ответ.
     * @throws Exception
     */
    private function validateQuestionToAnswer($questionId){
        $answeredQuestionsIds = $this->_testSession->getAnsweredQuestionsIds();

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
    private function validateAttemptNumber($userId, $testId){
        $test = $this->_testManager->getById($testId);

        $attemptsAllowed = $test->getAttempts();

        $extraAttempts = $this->_testResultManager->getExtraAttemptsCount($userId, $testId);
        $lastAttempt = $this->_testManager->getTestAttemptsUsedCount($testId, $userId);

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
    private function checkAnswers($questionInfo, $questionAnswer){
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
    private function saveQuestionAnswer($session, $questionId, $rightPercentage, $answerText = ''){
        $testResultId = $session->getTestResultId();
        $testResult = $this->_testResultManager->getById($testResultId);
        $question = $this->_questionManager->getById($questionId);

        if ($testResultId == null || $question == null){
            throw new Exception('Не удалось сохранить ответ!');
        }

        $givenAnswer = new GivenAnswer();
        $givenAnswer->setTestResult($testResult);
        $givenAnswer->setQuestion($question);
        $givenAnswer->setRightPercentage($rightPercentage);
        $givenAnswer->setAnswer($answerText);

        $this->_questionManager->createQuestionAnswer($givenAnswer);
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
    private function getAnswerText($questionAnswer, $studentAnswer){
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

    /**
     * Проверка наличия неотвеченных вопросов теста.
     * При отсутствии таковых, тест будет завершен.
     */
    private function checkIfNotAnsweredQuestionsExists(){
        $answered = $this->_testSession->getAnsweredQuestionsIds();
        $all = $this->_testSession->getAllQuestionsIds();

        if (count($answered) === count($all)){
            $this->processTestEnd();
        }
    }

}