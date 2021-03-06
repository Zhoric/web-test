<?php
use TestEngine\QuestionAnswer;

/**
 * Interface ITestProcessStrategy
 * Интерфейс, определяющий методы взаимодействия со стратегией процесса тестирования студентов.
 */
interface ITestProcessStrategy
{
    /**
     * Инициализация процесса тестирования.
     * Инициализация включает в себя:
     * - проверку доступности теста для студента;
     * - определение списка вопросов, которые будут представлены студенту;
     * - создание сущности "Результат теста" для последующей привязки к ней ответов студента.
     * Метод возвращает идентификатор сессии тестирования, использующийся для получения вопросов
     * и отправки ответов студентом.
     * @param $userId - Идентификатор пользователя, проходящего тестирование.
     * @param $testId - Идентификатор теста.
     */
    public function init($userId, $testId);

    /**
     * Получение очередного вопроса теста. Включает:
     * - проверку наличия вопросов теста, на которые ещё не был дан ответ;
     * - выбор очередного вопроса из списка;
     * - сохранение вопроса в списке выданных студенту.
     * В случае, если студент дал ответы на все вопросы, при вызове данного метода будет
     * возвращён результат теста, а не очередной вопрос.
     * @param $sessionId - Идентификатор сессии тестирования, полученный при инициализации процесса.
     */
    public function getNextQuestion($sessionId);

    /**
     * Обработка ответа на вопрос. Включает:
     * - проверку актуальности ответа (истечение отведённого времени, повторные ответы на один вопрос);
     * - подсчёт оценки за ответ, если это представляется возможным (если ответ открытый - его проверкой
     * занимается преподаватель);
     * - сохранение ответа на вопрос и его привязка к результату теста.
     * @param $sessionId - Идентификатор сессии тестирования, полученный при инициализации процесса.
     * @param QuestionAnswer $questionAnswer - Ответ, который дал студент.
     */
    public function processAnswer($sessionId, QuestionAnswer $questionAnswer);
}