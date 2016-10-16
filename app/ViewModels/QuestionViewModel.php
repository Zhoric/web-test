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
}