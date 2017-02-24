<?php

/**
 * Модель для отображения хронологии сдачи тестов студентом по определённой дисциплине
 * за выбранный промежуток времени.
 */
class TestPassingChronologyViewModel implements JsonSerializable
{
    /**
     * @var string - ФИО студента.
     */
    public $name;

    /**
     * @var integer - Средний балл.
     */
    public $mark;

    /**
     * @var array - Список результатов тестов.
     */
    public $results;

    function jsonSerialize()
    {
        return array(
            'name' => $this->name,
            'mark' => $this->mark,
            'results' => $this->results
        );
    }
}