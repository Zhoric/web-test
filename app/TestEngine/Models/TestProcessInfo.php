<?php

namespace TestEngine;


use JsonSerializable;

class TestProcessInfo implements JsonSerializable
{
    private $studentName;

    private $testName;

    private $testId;

    private $groupName;

    private $groupId;

    private $disciplineName;

    private $disciplineId;

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
            'answeredQuestions' => $this->answeredQuestions,
            'groupId' => $this->groupId,
            'disciplineId' => $this->disciplineId,
            'testId' => $this->testId
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

    public function getTestName()
    {
        return $this->testName;
    }

    public function setTestName($testName)
    {
        $this->testName = $testName;
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

    public function getDisciplineName()
    {
        return $this->disciplineName;
    }

    public function setDisciplineName($disciplineName)
    {
        $this->disciplineName = $disciplineName;
    }

    public function getGroupName()
    {
        return $this->groupName;
    }

    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    public function getTestId()
    {
        return $this->testId;
    }

    public function setTestId($testId)
    {
        $this->testId = $testId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    public function getDisciplineId()
    {
        return $this->disciplineId;
    }

    public function setDisciplineId($disciplineId)
    {
        $this->disciplineId = $disciplineId;
    }
}