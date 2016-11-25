<?php

/**
 * Типы вопросов теста
 */
abstract class QuestionType
{
    /**
     * Закрытый вопрос с одним правильным вариантом ответа.
     */
    const ClosedOneAnswer = 1;

    /**
     * Закрытый вопрос с несколькими вариантами ответа.
     */
    const ClosedManyAnswers = 2;

    /**
     * Открытый вопрос с однострочным ответом.
     */
    const OpenOneString = 3;

    /**
     * Открытый вопрос с многострочным ответом (проверяется вручную).
     */
    const OpenManyStrings = 4;

    /**
     * Вопрос с программным кодом.
     */
    const WithProgram = 5;
}