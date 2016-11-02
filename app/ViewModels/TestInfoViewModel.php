<?php

/**
 * Информация о тесте для студента.
 */
class TestInfoViewModel implements JsonSerializable
{
    private $_test;

    private $_attemptsLeft;

    private $_attemptsMade;

    private $_lastMark;

    /**
     * @return mixed
     */
    public function getTest()
    {
        return $this->_test;
    }

    /**
     * @param mixed $test
     */
    public function setTest($test)
    {
        $this->_test = $test;
    }

    /**
     * @return mixed
     */
    public function getAttemptsLeft()
    {
        return $this->_attemptsLeft;
    }

    /**
     * @param mixed $attemptsLeft
     */
    public function setAttemptsLeft($attemptsLeft)
    {
        $this->_attemptsLeft = $attemptsLeft;
    }

    /**
     * @return mixed
     */
    public function getAttemptsMade()
    {
        return $this->_attemptsMade;
    }

    /**
     * @param mixed $attemptsMade
     */
    public function setAttemptsMade($attemptsMade)
    {
        $this->_attemptsMade = $attemptsMade;
    }

    /**
     * @return mixed
     */
    public function getLastMark()
    {
        return $this->_lastMark;
    }

    /**
     * @param mixed $maxMark
     */
    public function setLastMark($maxMark)
    {
        $this->_lastMark = $maxMark;
    }


    function jsonSerialize()
    {
        return array(
            'test' => $this->_test,
            'attemptsMade' => $this->_attemptsMade,
            'attemptsLeft' => $this->_attemptsLeft,
            'lastMark' => $this->_lastMark,
        );
    }
}