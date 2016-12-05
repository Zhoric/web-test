<?php

namespace TestEngine;

use Exception;
use GivenAnswer;
use Managers\SettingsManager;
use QuestionType;
use Repositories\UnitOfWork;

class TestResultCalculator
{
    /**
     * @var UnitOfWork
     */
    private static $_unitOfWork;
    private static $_settingsManager;

    private static function getUnitOfWork(){
        if (self::$_unitOfWork == null){
            self::$_unitOfWork = app()->make(UnitOfWork::class);
        }
        return self::$_unitOfWork;
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
     * Подсчёт оценки за тест.
     * @param $testResultId - id сущности результата теста.
     * @return float
     * @throws Exception
     */
    public static function calculate($testResultId){
        $testResult = self::getUnitOfWork()->testResults()->find($testResultId);
        if ($testResult == null){
            throw new Exception("Не удалось сформировать результат теста!");
        }

        //Получение значения настройки, отвечающей за максимальное значение для оценки студента.
        $maxMarkValue = self::getSettingsManager()->get(GlobalTestSettings::maxMarkValueKey);

        $resultPercents = self::getResultPercents($testResultId);
        $resultMark = ($resultPercents !== null)
            ? ($resultPercents / 100) * $maxMarkValue
            : null;

        if ($resultMark !== null){
            $resultMark = floor($resultMark);
            $resultMark = $resultMark > 100 ? 100 : $resultMark;
            $resultMark = $resultMark < 0 ? 0 : $resultMark;
        }

        return $resultMark;
    }

    /**
     * Установка баллов за ответ вручную с последующим пересчётом общего результата за тест.
     * @param $givenAnswerId - Ответ сдудента на вопрос.
     * @param $mark - Оценка.
     * @return float - Возвращает общую оценку за тест, посчитанную с учётом проставленной оценки за ответ.
     * Если по тесту ещё остались непроверенные ответы, будет возвращён null.
     * @throws Exception
     */
    public static function setAnswerMark($givenAnswerId, $mark){
        $givenAnswer = self::getUnitOfWork()->givenAnswers()->find($givenAnswerId);
        if (!isset($givenAnswer)){
            throw new Exception('Не найден указанный ответ студента!');
        }

        $testResult = $givenAnswer->getTestResult();
        if (!isset($testResult)) {
            throw new Exception('Невозможно получить результат студента по пройденному тесту!');
        }

        $givenAnswer->setRightPercentage($mark);
        self::getUnitOfWork()->givenAnswers()->update($givenAnswer);
        self::getUnitOfWork()->commit();

        $newResultMark =  self::calculate($testResult->getId());
        $testResult->setMark($newResultMark);
        self::getUnitOfWork()->testResults()->update($testResult);
        self::getUnitOfWork()->commit();

        return $newResultMark;
    }

    private static function getResultPercents($testResultId){
        $maxMark = 0;
        $studentMark = 0;
        $answers = self::getUnitOfWork()->givenAnswers()->getByTestResult($testResultId);

        foreach ($answers as $answer) {
            $answer = self::processNullPointsAnswer($answer);
            if ($answer->getRightPercentage() === null) {
                return null;
            }

            $question = $answer->getQuestion();
            $complexity = $question->getComplexity();
            $complexity = ($complexity != null) ? $complexity : GlobalTestSettings::defaultComplexity;

            $maxMark += $complexity * GlobalTestSettings::complexityDifferenceCoef;
            $studentMark += $complexity * $answer->getRightPercentage() * GlobalTestSettings::complexityDifferenceCoef;
        }
        return $maxMark != 0 ? ceil($studentMark/$maxMark) : 0;
    }

    /**
     * Обработка ответа без оценки.
     * Если оценка отсутствует у ответа на открытый многострочный вопрос - общий результат также невозможно подсчитать.
     * Необходима проверка преподавателя.
     * Если не проставлена оценка за другой тип вопроса, значит, ответ не был дан и будет оценён в 0 баллов.
     * @param GivenAnswer $answer
     * @return GivenAnswer - Возвращает ответ на вопрос с
     * @throws Exception
     */
    private static function processNullPointsAnswer(GivenAnswer $answer){

        if ($answer->getRightPercentage() === null) {
            $questionAnswered = $answer->getQuestion();
            if (!isset($questionAnswered)) {
                throw new Exception('Невозможно подсчитать результат! Отсутствуют данные о вопросе!');
            }

            if ($questionAnswered->getType() !== QuestionType::OpenManyStrings) {
                $answer->setRightPercentage(0);

                self::$_unitOfWork->givenAnswers()->update($answer);
                self::$_unitOfWork->commit();
            }
        }
        return $answer;
    }

}