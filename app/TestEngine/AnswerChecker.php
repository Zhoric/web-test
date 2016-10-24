<?php

namespace TestEngine;


/**
 * Class AnswerChecker - отвечает за проверку правильности ответов.
 */
class AnswerChecker
{
    /**
     * Подсчёт оценки (в процентах) за ответ на вопрос.
     * @param $answers - все варианты ответа.
     * @param $studentAnswers - варианты ответа, которые дал студент.
     * @return int - оценка, %.
     */
    public static function calculatePointsForAnswer(array $answers, array $studentAnswers){
        $totalRightAnswersCount = self::calculateTotalRightAnswers($answers);
        $studentRightAnswersCount = self::calculateRightStudentAnswers($answers, $studentAnswers);

        $rightPercentage = $studentRightAnswersCount/$totalRightAnswersCount * 100;
        $rightPercentageRounded = floor($rightPercentage);

        return $rightPercentageRounded;
    }

    /**
     * Подсчёт общего количества верных ответов в вопросе.
     * @param $answers - все варианты ответа.
     * @return int - общее количество верных ответов.
     */
    private static function calculateTotalRightAnswers($answers){
        $rightAnsCount = 0;

        for ($i = 0; $i < count($answers); $i++){
            if (self::isRight($answers[$i])) $rightAnsCount++;
        }

        return $rightAnsCount;
    }

    /**
     * Подсчёт общего количества верных ответов среди тех, которые дал студент.
     * За каждый правильный ответ добавляется 1, за каждый неправильный - вычитается.
     * @param $answers - все варианты ответа.
     * @return int - общее количество верных ответов.
     */
    private static function calculateRightStudentAnswers(array $answers, array $studentAnswers){
        $rightAnswers = 0;

        for ($i = 0; $i < count($answers); $i++){
            if (in_array($answers[$i]->getId(), $studentAnswers)){
                self::isRight($answers[$i]) ? $rightAnswers++ : $rightAnswers--;
            }
        }

        return $rightAnswers > 0 ? $rightAnswers : 0;
    }

    /**
     * @param \Answer $answer
     * @return bool - Верен ли данный ответ.
     */
    private static function isRight($answer){
        return $answer->getIsRight();
    }
}