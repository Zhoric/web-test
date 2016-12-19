<?php

namespace TestEngine;


use JsonSerializable;

class TestProcessInfo implements JsonSerializable
{
    private $studentName;

    private $testName;

    private $groupName;

    private $disciplineName;

    private $allQuestions;

    private $answeredQuestions;

    function jsonSerialize()
    {
        return array(
            'studentName' => $this->studentName,
            'testName' => $this->testName,
            'disciplineName' => $this->disciplineName,
            'groupName' => $this->groupName,
            'allQuestions' => $this->allQuestions,
            'answeredQuestions' => $this->answeredQuestions
        );
    }

    /**
     * @return mixed
     */
    public function getStudentName()
    {
        return $this->studentName;
    }

    /**
     * @param mixed $studentName
     */
    public function setStudentName($studentName)
    {
        $this->studentName = $studentName;
    }

    /**
     * @return mixed
     */
    public function getTestName()
    {
        return $this->testName;
    }

    /**
     * @param mixed $testName
     */
    public function setTestName($testName)
    {
        $this->testName = $testName;
    }

    /**
     * @return mixed
     */
    public function getAllQuestions()
    {
        return $this->allQuestions;
    }

    /**
     * @param mixed $allQuestions
     */
    public function setAllQuestions($allQuestions)
    {
        $this->allQuestions = $allQuestions;
    }

    /**
     * @return mixed
     */
    public function getAnsweredQuestions()
    {
        return $this->answeredQuestions;
    }

    /**
     * @param mixed $answeredQuestions
     */
    public function setAnsweredQuestions($answeredQuestions)
    {
        $this->answeredQuestions = $answeredQuestions;
    }

    /**
     * @return mixed
     */
    public function getDisciplineName()
    {
        return $this->disciplineName;
    }

    /**
     * @param mixed $disciplineName
     */
    public function setDisciplineName($disciplineName)
    {
        $this->disciplineName = $disciplineName;
    }

    /**
     * @return mixed
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param mixed $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }
}