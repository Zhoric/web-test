<?php

/**
 * Критерий выборки результатов студента по тесту.
 */
abstract class ResultSelectionCriterion
{
    /**
     * Максимальная оценка.
     */
    const MaxMark = 1;

    /**
     * Первая попытка.
     */
    const FirstAttempt = 2;

    /**
     * Последняя попытка.
     */
    const LastAttempt = 3;




}