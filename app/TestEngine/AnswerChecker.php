<?php

namespace TestEngine;
use CodeQuestionEngine\CodeQuestionManager;
use Exception;
use Repositories\UnitOfWork;


/**
 * Class AnswerChecker - отвечает за проверку правильности ответов.
 */
class AnswerChecker
{
    private static $_codeQuestionManager;
    private static $_unitOfWork;

    /**
     * @return UnitOfWork
     */
    private static function getUnitOfWork(){
        if (self::$_unitOfWork == null){
            self::$_unitOfWork = app()->make(UnitOfWork::class);
        }
        return self::$_unitOfWork;
    }

    /**
     * Получение менеджера вопросов с программным кодом для подсчёта оценки за ответ на
     * вопрос с кодом.
     * @return CodeQuestionManager
     */
    private static function getCodeQuestionManager(){
        if (self::$_codeQuestionManager == null){
            self::$_codeQuestionManager = app()->make(CodeQuestionManager::class);
        }
        return self::$_codeQuestionManager;
    }

    /**
     * Подсчёт оценки (в процентах) за ответ на закрытый вопрос.
     * @param $answers - Все варианты ответа.
     * @param $studentAnswers - Варианты ответа, которые дал студент.
     * @return int - Оценка, %.
     */
    public static function calculatePointsForClosedAnswer($answers, $studentAnswers){

        $studentAnswers = ($studentAnswers == null) ? [] : $studentAnswers;
        $totalRightAnswersCount = self::calculateTotalRightAnswers($answers);
        $studentRightAnswersCount = self::calculateRightStudentAnswers($answers, $studentAnswers);

        $rightPercentage = $studentRightAnswersCount/$totalRightAnswersCount * 100;
        $rightPercentageRounded = floor($rightPercentage);

        return $rightPercentageRounded;
    }

    /**
     * Подсчёт оценки за ответ на открытый однострочный вопрос.
     * Ответ считается правильным, если он совпал хотя бы с одним из правильных вариантов.
     * @param $answers - Правильные варианты ответа.
     * @param $studentAnswerText - Текст ответа, который дал студент.
     * @return int - Оценка, %.
     */
    public static function calculatePointsForSingleStringAnswer($answers, $studentAnswerText){
        foreach ($answers as $answer){
            $rightAnswerText = $answer->getText();
            if (self::prepareForComparison($rightAnswerText) == self::prepareForComparison($studentAnswerText)){
                return 100;
            }
        }
        return 0;
    }

    /**
     * Подсчёт оценки за ответ на вопрос с программным кодом.
     * @param $questionId
     * @param $studentCode - код, написанный студентом.
     * @return mixed
     * @throws \Exception
     * @internal param $questionId - идентификатор вопроса.
     */
    public static function calculatePointsForProgramAnswer($questionId, $studentCode){
        $program = self::getUnitOfWork()->programs()->getByQuestion($questionId);
        if (!isset($program)){
            throw new Exception('По данному вопросу не найден программный код!');
        }
        $programId = $program->getId();

        
            $rightPercentage = self::getCodeQuestionManager()->runQuestionProgram($studentCode, $programId);


        return $rightPercentage;
    }

    /**
     * Подготовка строки ответа к сравнению.
     * @param $string - Входная строка.
     * @return string
     */
    private static function prepareForComparison($string){
        preg_replace("/(^\\s+)|(\\s+$)/us", "", $string);
        return mb_strtoupper(($string));
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