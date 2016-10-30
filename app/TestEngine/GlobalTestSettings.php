<?php

namespace TestEngine;

/**
 * Глобальные настройки механизма тестирования.
 */
abstract class GlobalTestSettings
{
    // Значение сложности вопроса по умолчанию.
    const defaultComplexity = 1;

    // Максимальная оценка за тест.
    const maxMarkValue = 100;

    // Допуск времени (в секундах), отведённого на тест.
    const testEndTolerance = 30;
}