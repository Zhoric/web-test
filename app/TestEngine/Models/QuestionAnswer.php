<?php

namespace TestEngine;

class QuestionAnswer
{
    /**
     * Id вопроса, на который дан ответ.
     */
    private $questionId;

    /**
     * Текст ответа.
     */
    private $answerText;

    /**
     * Id выбранных ответов.
     */
    private $answerIds;

    /**
     * @return mixed
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @param mixed $questionId
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;
    }

    /**
     * @return mixed
     */
    public function getAnswerText()
    {
        return $this->answerText;
    }

    /**
     * @param mixed $answerText
     */
    public function setAnswerText($answerText)
    {
        $this->answerText = $answerText;
    }

    /**
     * @return mixed
     */
    public function getAnswerIds()
    {
        return $this->answerIds;
    }

    /**
     * @param mixed $answerIds
     */
    public function setAnswerIds($answerIds)
    {
        $this->answerIds = $answerIds;
    }

    public function fillFromJson($json){
        $jsonArray = $json;
        foreach($jsonArray as $key=>$value){
            $this->$key = $value;
        }
    }

}