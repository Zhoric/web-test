<?php

namespace TestEngine;

use Exception;
use Repositories\UnitOfWork;

class TestResultCalculator
{
    /**
     * @var UnitOfWork
     */
    private static $_unitOfWork;

    private static function getUnitOfWork(){
        if (self::$_unitOfWork == null){
            self::$_unitOfWork = app()->make(UnitOfWork::class);
        }
        return self::$_unitOfWork;
    }

    /**
     * Подсчёт оценки за тест.
     * @param $testResultId - id сущности результата теста.
     * @return float
     * @throws Exception
     */
    public static function calculate($testResultId){
        //DEBUG HARDCODE
        $testResult = self::getUnitOfWork()->testResults()->find($testResultId);
        if ($testResult == null){
            throw new Exception("Не удалось сформировать результат теста!");
        }

        $resultPercents = self::getResultPercents($testResultId);
        $resultMark = ($resultPercents != null)
            ? ($resultPercents / 100) * GlobalTestSettings::maxMarkValue
            : null;

        return $resultMark;
    }

    private static function getResultPercents($testResultId){
        $maxMark = 0;
        $studentMark = 0;
        $answers = self::getUnitOfWork()->givenAnswers()->getByTestResult($testResultId);

        foreach ($answers as $answer) {

            if ($answer->getRightPercentage() != 0 && $answer->getRightPercentage() == null) {
                return null;
            }

            $question = $answer->getQuestion();
            $complexity = $question->getComplexity();
            $complexity = ($complexity != null) ? $complexity : GlobalTestSettings::defaultComplexity;

            $maxMark += $complexity * GlobalTestSettings::complexityDifferenceCoef;
            $studentMark += $complexity * $answer->getRightPercentage() * GlobalTestSettings::complexityDifferenceCoef;
        }

        return ceil($studentMark/$maxMark);
    }
}