<?php

namespace TestEngine;


use JsonSerializable;

/**
 * Модель для представления информации о состоянии теста в реальном времени.
 * Class TestProcessInfo
 * @package TestEngine
 */
class TestProcessInfo implements JsonSerializable
{
    private $studentName;

    private $allQuestions;

    private $answeredQuestions;

    private $mark;

    private $state;

    function jsonSerialize()
    {
        return array(
            'studentName' => $this->studentName,
            'allQuestions' => $this->allQuestions,
            'answeredQuestions' => $this->answeredQuestions,
            'mark' => $this->mark,
            'state' => $this->state
        );
    }

    public function getStudentName()
    {
        return $this->studentName;
    }

    public function setStudentName($studentName)
    {
        $this->studentName = $studentName;
    }

    public function getAllQuestions()
    {
        return $this->allQuestions;
    }

    public function setAllQuestions($allQuestions)
    {
        $this->allQuestions = $allQuestions;
    }

    public function getAnsweredQuestions()
    {
        return $this->answeredQuestions;
    }

    public function setAnsweredQuestions($answeredQuestions)
    {
        $this->answeredQuestions = $answeredQuestions;
    }

    public function getMark()
    {
        return $this->mark;
    }

    public function setMark($mark)
    {
        $this->mark = $mark;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
}