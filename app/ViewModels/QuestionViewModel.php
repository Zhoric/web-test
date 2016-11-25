<?php

class QuestionViewModel implements JsonSerializable
{
    private $_question;
    private $_answers;
    private $_program;
    private $_paramSets;

    public function __construct($question, $answers, $program = null, $paramSets = null)
    {
        $this->_question = $question;
        $this->_answers = $answers;
        $this->_program = $program;
        $this->_paramSets = $paramSets;
    }

    function jsonSerialize()
    {
        return array(
            'question' => $this->_question,
            'answers' => $this->_answers,
            'program' => $this->_program,
            'paramSets' => $this->_paramSets
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