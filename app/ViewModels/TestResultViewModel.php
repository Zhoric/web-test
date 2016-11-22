<?php

class TestResultViewModel implements JsonSerializable
{
    private $_testResult;
    private $_answers;
    private $_test;
    private $_attemptsAllowed;

    public function __construct($question, $answers, $test = null, $attemptsAllowed = null)
    {
        $this->_testResult = $question;
        $this->_answers = $answers;
        $this->_test = $test;
        $this->_attemptsAllowed = $attemptsAllowed;
    }

    function jsonSerialize()
    {
        return array(
            'testResult' => $this->_testResult,
            'answers' => $this->_answers,
            'test' => $this->_test,
            'attemptsAllowed' => $this->_attemptsAllowed
        );
    }

    /**
     * @return mixed
     */
    public function getTestResult()
    {
        return $this->_testResult;
    }

    /**
     * @param mixed $testResult
     */
    public function setTestResult($testResult)
    {
        $this->_testResult = $testResult;
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->_answers;
    }

    /**
     * @param mixed $answers
     */
    public function setAnswers($answers)
    {
        $this->_answers = $answers;
    }


}