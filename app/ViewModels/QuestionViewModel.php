<?php

class QuestionViewModel implements JsonSerializable
{
    private $_question;
    private $_answers;

    public function __construct($question, $answers)
    {
        $this->_question = $question;
        $this->_answers = $answers;
    }

    function jsonSerialize()
    {
        return array(
            'question' => $this->_question,
            'answers' => $this->_answers
        );
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->_question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->_question = $question;
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