<?php

namespace TestEngine;
use QuestionComplexity;

/**
 * Глобальные настройки механизма тестирования.
 */
abstract class GlobalTestSettings
{
    /**
     * Значение сложности вопроса по умолчанию.
     */
    const defaultComplexityKey = 'defaultComplexity';
    const defaultComplexity = QuestionComplexity::Low;

    /**
     * Максимальная оценка за тест.
     */
    const maxMarkValueKey = 'maxMarkValue';
    const maxMarkValue = 100;

    /**
     * Допуск времени (в секундах), отведённого на тест.
     * Т.е. время, в течение которого ещё можно отправить ответ, если время теста истекло.
     */
    const testEndToleranceKey = 'testEndTolerance';
    const testEndTolerance = 30;

    /**
     * Допуск времени (в секундах), отведённого на текущий вопрос.
     * Т.е. время, в течение которого ещё можно отправить ответ на текущий вопрос,
     * если время, отведённое на данный вопрос, истекло.
     */
    const questionEndToleranceKey = 'questionEndTolerance';
    const questionEndTolerance = 5;

    /**
     * Коэффициент различия баллов, получаемых за вопросы различной сложности.
     */
    const complexityDifferenceCoef = 0.6;

    /**
     * Номер месяца, с которого отсчитывается первый семестр.
     */
    const firstSemesterMounthKey = 'firstSemesterMounth';
    const firstSemesterMounth = 8;

    /**
     * Номер месяца, с которого отсчитывается второй семестр.
     */
    const secondSemesterMounthKey = 'secondSemesterMounthKey';
    const secondSemesterMounth = 1;

    /**
     * Формат сериализации даты.
     */
    const dateSerializationFormat = 'Y-m-d H:i:s';

    /**
     * Часовой пояс.
     */
    const dateTimeZone = 'Europe/Moscow';

    /**
     * Минимальная оценка, при которой ответ на вопрос теста свидетельствует о достаточном освоении материала.
     * Настройка используется, например, для того, чтобы определить, стоит ли показывать студенту
     * в результатах обучающего тестирования тот или иной вопрос, в зависимости от правильности ответа.
     * [Для вопросов с автоматической проверкой]
     */
    const minAutoCheckGoodMark = 100;

    /**
     * Минимальная оценка, при которой ответ на вопрос теста свидетельствует о достаточном освоении материала.
     * [Для вопросов с ручной проверкой]
     */
    const minManualCheckGoodMark = 80;

}