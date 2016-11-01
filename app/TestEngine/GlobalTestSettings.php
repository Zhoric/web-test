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
    const defaultComplexity = QuestionComplexity::Low;

    /**
     * Максимальная оценка за тест.
     */
    const maxMarkValue = 100;

    /**
     * Допуск времени (в секундах), отведённого на тест.
     * Т.е. время, в течение которого ещё можно отправить ответ, если время теста истекло.
     */
    const testEndTolerance = 30;

    /**
     * Коэффициент различия баллов, получаемых за вопросы различной сложности.
     */
    const complexityDifferenceCoef = 0.6;
}