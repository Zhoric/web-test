<?php

namespace TestEngine;

use Exception;
use GivenAnswer;
use Managers\SettingsManager;
use QuestionComplexity;
use QuestionType;
use Repositories\UnitOfWork;

class TestResultCalculator
{
    /**
     * @var UnitOfWork
     */
    private static $_unitOfWork;
    private static $_settingsManager;

    private static $_complexQuestionMaxPoints;

    private static function getComplexQuestionMaxPoints(){
        if (self::$_complexQuestionMaxPoints == null){
            self::$_complexQuestionMaxPoints =
                self::getSettingsManager()->get(GlobalTestSettings::complexQuestionPointsKey);
        }
        return self::$_complexQuestionMaxPoints;
    }

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
        $answers = self::getUnitOfWork()->givenAnswers()->getByTestResult($testResultId);

        return self::calculateTestResult($answers);
    }

    /**
     * Расчёт промежуточной оценки за тест (в баллах) по текущим ответам студента.
     * [!] При расчёте будут проигнорированы вопросы, ответы на которые должны проверяться вручную,
     * а также ответы на вопросы с программным кодом, для которых ещё не вычислена оценка.
     * @param $testResultId - Идентификатор результата теста.
     * @return float|int|null - Возвращает оценку в баллах.
     */
    public static function calculateIntermediateResult($testResultId){
        $answers = self::getUnitOfWork()->givenAnswers()->getByTestResult($testResultId);
        return self::calculateTestResult($answers, true);
    }

    /**
     * Расчёт итоговой оценки за тест (в баллах) по списку ответов студента.
     * @param array $answers - Список ответов студента.
     * @param bool $isIntermediateResult - Признак того, что производится подсчёт промежуточной оценки (тест ещё не окончен).
     * @return float|int|null - Возвращает оценку в баллах.
     */
    public static function calculateTestResult(array $answers, $isIntermediateResult = false){
        $maxMark = $studentMark = 0;

        foreach ($answers as $answer) {
            //Обработка случая, когда оценка ответа на очередной вопрос оказалась равна null.
            $answer = self::processNullPointsAnswer($answer, $isIntermediateResult);
            //Если даже после processNullPointsAnswer, оценка за ответ по-прежнему null
            if ($answer->getRightPercentage() === null) {
                //Если выполняется подсчёт промежуточной оценки (тест ещё не закончился)
                if ($isIntermediateResult){
                    //Просто игнорируем ответ с оценкой null.
                    continue;
                }
                //В противнои случае вычисление итоговой оценки в данный момент невозможно и должно будет произойти позже,
                //когда будут проверены все открытые вопросы и вопросы с программным кодом.
                else {
                    return null;
                }
            }
            //Получаем сложность вопроса, на который был дан текущий ответ и кол-во баллов, которые могут быть начислены за ответ на вопрос такого типа.
            $complexity = $answer->getQuestion()->getComplexity();
            $maxAnswerPoints = self::getMaxPointsForAnswer($complexity);

            //Максимально возможная оценка = Сумма[максимальная_оценка_за_вопрос(i)], i = 1..n.
            $maxMark += $maxAnswerPoints;
            //Оценка студента = Сумма[максимальная_оценка_за_вопрос(i) * качество_ответа(i)], i = 1..n.
            $studentMark += $maxAnswerPoints * $answer->getRightPercentage();
        }
        //Относительная оценка (в процентах) = Оценка студента/Максимально возможная оценка.
        $markInPercents =  $maxMark != 0 ? ceil($studentMark/$maxMark) : 0;

        //Получение значения настройки, отвечающей за максимальное значение для оценки студента (в баллах).
        $maxMarkValue = self::getSettingsManager()->get(GlobalTestSettings::maxMarkValueKey);

        //Расчёт абсолютной итоговой оценки (в баллах). Оценка = Оценка в процентах / 100% * Максимально возможная оценка.
        $resultMark = ($markInPercents !== null) ? ($markInPercents / 100) * $maxMarkValue : null;

        //Округление оценки и приведение её к значению в интервале [0; Максимально возможная оценка.]
        if ($resultMark !== null){
            $resultMark = floor($resultMark);
            $resultMark = $resultMark > $maxMarkValue ? $maxMarkValue : $resultMark;
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

        /** @var \TestResult $testResult */
        $testResult = $givenAnswer->getTestResult();
        if (!isset($testResult)) {
            throw new Exception('Невозможно получить результат студента по пройденному тесту!');
        }

        //Установка новой оценки за ответ.
        $givenAnswer->setRightPercentage($mark);
        self::getUnitOfWork()->givenAnswers()->update($givenAnswer);
        self::getUnitOfWork()->commit();

        //Пересчёт и сохранение итоговой оценки за тест с учётом новой оценки за ответ.
        $newResultMark =  self::calculate($testResult->getId());
        $testResult->setMark($newResultMark);
        self::getUnitOfWork()->testResults()->update($testResult);
        self::getUnitOfWork()->commit();

        return $newResultMark;
    }

    /**
     * Обработка ответа без оценки.
     * Если оценка отсутствует у ответа на открытый многострочный вопрос
     * или на вопрос с программным кодом - общий результат также невозможно подсчитать.
     * Необходима проверка преподавателя (в случае открытого вопроса)
     * или обработка вопроса модулем проверки вопросов с кодом (в случае вопроса с программным кодом).
     * В этом случае оценка за ответ так и останется равной null.
     * Если не проставлена оценка за другой тип вопроса, значит, ответ не был дан студентом и будет оценён в 0 баллов.
     * @param GivenAnswer $answer - Ответ на вопрос теста, который дал студент.
     * @param bool $isIntermediateResult - Признак того, что производится подсчёт промежуточной оценки (тест ещё не окончен).
     * @return GivenAnswer - Возвращает ответ на вопрос с изменённой оценкой за него.
     * @throws Exception
     */
    private static function processNullPointsAnswer(GivenAnswer $answer, $isIntermediateResult){

        if ($answer->getRightPercentage() === null) {
            $questionAnswered = $answer->getQuestion();
            if (!isset($questionAnswered)) {
                throw new Exception('Невозможно подсчитать результат! Отсутствуют данные о вопросе!');
            }

            $questionType = $questionAnswered->getType();

            if ($questionType !== QuestionType::OpenManyStrings && $questionType !== QuestionType::WithProgram) {
                $answer->setRightPercentage(0);

                if (!$isIntermediateResult){
                    // Если подсчёт оценки не промежуточный, нужно также обновить оценку в БД.
                    self::$_unitOfWork->givenAnswers()->update($answer);
                    self::$_unitOfWork->commit();
                }
            }
        }
        return $answer;
    }

    /**
     * Получение максимальной оценки, которая может быть начислена за вопрос указанной сложности.
     * Это число используется для возможности корректировки шкалы оценивания для вопросов разной сложности администратором.
     * Значения максимальных оценок за вопросы разной сложности могут быть установлены в разделе "Настройки" страницы администрирования.
     * @param $questionComplexity - Сложность вопроса.
     * @return int - Возвращает максимальную оценку, которая может быть получена за вопрос указанной сложности.
     * @throws Exception
     */
    private static function getMaxPointsForAnswer($questionComplexity){
        switch ($questionComplexity){
            case QuestionComplexity::Low:
                return 1;
            case QuestionComplexity::High:
                return self::getComplexQuestionMaxPoints();
            case QuestionComplexity::Medium:
                return (1 + self::getComplexQuestionMaxPoints()) / 2;
            default: throw new Exception('Невозможно подсчитать оценку за ответ. Не указана сложность вопроса!');
        }
    }

}