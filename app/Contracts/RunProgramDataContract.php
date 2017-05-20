<?php


class RunProgramDataContract extends BaseContract implements JsonSerializable
{
    /**
     * @var string текст программы
     */
    private $code;

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

    /**
     * @return int
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * @param int $programId
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @var int id сущности Program из бд
     */
    private $programId;

    /**
     * @var int id сущности testResult из бд
     */
    private $testResultId;

    /**
     * @var int id сущности Question из бд
     */
    private $questionId;

    /**
     * @var int id сущности User из бд
     */
    private $userId;


    public function jsonSerialize()
    {
        return array(
            'code' => $this->code,
            'programId' => $this->programId,
            'testResultId' => $this->testResultId,
            'questionId' => $this->questionId,
            'userId' => $this->userId,
        );
    }
}