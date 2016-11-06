<?php

class TestResultViewModel implements JsonSerializable
{
    private $_testResult;
    private $_answers;

    public function __construct($question, $answers)
    {
        $this->_testResult = $question;
        $this->_answers = $answers;
    }

    function jsonSerialize()
    {
        return array(
            'testResult' => $this->_testResult,
            'answers' => $this->_answers
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