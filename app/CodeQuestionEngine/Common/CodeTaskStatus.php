<?php


/**
 * Class CodeTaskStatus
 * Состояния задачи с кодом
 */
class CodeTaskStatus
{
    /**
     * В очереди на выполнение
     */
    const QueuedToExecute = 0;

    /**
     * Выполняется
     */
    const Running = 1;

    /**
     * Остановлена, т.к. вышла за ограничение по времени
     */
    const Timeout = 2;

    /**
     * Остановлена, т.к. вышла за ограничение по памяти
     */
    const MemoryOverflow = 3;

    /**
     * В очереди на проверку
     */
    const QueuedToCheck = 4;


    /**
     * Проверяется
     */
    const Checking = 5;

    /**
     * Выполнена и проверена
     */
    const Done = 6;
}