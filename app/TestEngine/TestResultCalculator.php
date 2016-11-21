<?php

namespace TestEngine;

use Exception;
use Managers\SettingsManager;
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
        $resultMark = ($resultPercents != null)
            ? ($resultPercents / 100) * $maxMarkValue
            : null;

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

            if ($answer->getRightPercentage() == null) {
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

}