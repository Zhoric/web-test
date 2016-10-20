<?php

namespace TestEngine;
use League\Flysystem\Exception;

/**
 * Класс, ответственный за управление процессом тестирования.
 */
class TestProcessManager
{
    /**
     * Инициализация процесса тестирования.
     */
    public static function initTest($userId, $testId){
        $sessionId = TestSessionHandler::createTestSession($userId, $testId);
        //TODO: Создание сущности TestResult, id которой также можно хранить в кэше
        //TODO: Проверка на количество попыток
        return $sessionId;
    }

    /**
     * Получение следующего вопроса на основании настроек теста
     * и списка вопросов, на которые уже были даны ответы.
     */
    public static function getNextQuestion($sessionId){
        try{
            $session = TestSessionHandler::getSession($sessionId);
            $testId = $session->getTestId();
            $answeredQuestionsIds = $session->getAnsweredQuestionsIds();
            //TODO: Проверка на истечение сессии тестирования (подобрать погрешность)
            //TODO: Проверка на отсутствие сессии с таким идентификатором

            $testManager = TestSessionHandler::getTestManager();
            $test = $testManager->getById($testId);
            /* TODO: На основании настроек теста выбрать следующий вопрос
            *  TODO: Учитывать оставшееся на тест время при поиске подходящих вопросов,
            *  переводя время до конца теста в секунды и сравнивая с временем на вопрос
            */

            $suitableQuestions = $testManager->getNotAnsweredQuestionsByTest(
                $testId,
                $answeredQuestionsIds);

            if ($suitableQuestions == null || empty($suitableQuestions)){
                throw new Exception('Тест завершен!');
            }

            //TODO: Обработка случая, когда подходящих вопросов не осталось (завершение теста)

            array_flatten($suitableQuestions);
            $nextQuestionIndex = array_rand($suitableQuestions);
            $nextQuestionId = $suitableQuestions[$nextQuestionIndex]['id'];

            array_push($answeredQuestionsIds, $nextQuestionId);
            $session->setAnsweredQuestionsIds($answeredQuestionsIds);
            TestSessionHandler::updateSession($sessionId, $answeredQuestionsIds);
        } catch (Exception $exception){
            return array('message' => $exception->getMessage());
        }

        return $testManager->getQuestionWithAnswers($nextQuestionId);
    }

}