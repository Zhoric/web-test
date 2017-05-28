<?php


class GivenAnswerData extends BaseContract implements JsonSerializable
{

    /**
     * @var string текст программы
     */
    protected $code;


    /**
     * @var int id сущности testResult из бд
     */
    protected $testResultId;


    /**
     * @var int id сущности Question из бд
     */
    protected $questionId;

    /**
     * @return int
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @param int $questionId
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;
    }


    /**
     * @return int
     */
    public function getTestResultId()
    {
        return $this->testResultId;
    }

    /**
     * @param int $testResultId
     */
    public function setTestResultId($testResultId)
    {
        $this->testResultId = $testResultId;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    public function jsonSerialize()
    {
        return array(
            'code' => $this->code,
            'testResultId' => $this->testResultId,
            'questionId' => $this->questionId,
        );
    }
}