<?php


class RunProgramDataContract extends BaseContract implements JsonSerializable
{
    /**
     * @var string текст программы
     */
    protected $code;


    /**
     * @var array ParamsSet
     */
    protected $paramSets;


    /**
     * @var int id сущности Program из бд
     */
    protected $programId;

    /**
     * @var int id сущности testResult из бд
     */
    protected $testResultId;

    /**
     * @var int id сущности Question из бд
     */
    protected $questionId;

    /**
     * @var string язык программирования
     */
    protected $language;


    /**
     * @var int Лимит памяти на программу
     */
    protected $memoryLimit;


    /**
     * @var string Фамилия_Имя_Отчество пользователя
     */
    protected $fio;


    /**
     * @var int Лимит времени на программу
     */
    protected $timeLimit;


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
     * @return string
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * @param string $fio
     */
    public function setFio($fio)
    {
        $this->fio = $fio;
    }

    /**
     * @return int
     */
    public function getMemoryLimit()
    {
        return $this->memoryLimit;
    }

    /**
     * @param int $memoryLimit
     */
    public function setMemoryLimit($memoryLimit)
    {
        $this->memoryLimit = $memoryLimit;
    }

    /**
     * @return int
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * @param int $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return array
     */
    public function getParamSets()
    {
        return $this->paramSets;
    }

    /**
     * @param array $paramSets
     */
    public function setParamSets($paramSets)
    {
        $this->paramSets = $paramSets;
    }


    public function jsonSerialize()
    {
        return array(
            'code' => $this->code,
            'programId' => $this->programId,
            'testResultId' => $this->testResultId,
            'questionId' => $this->questionId,
            'language' => $this->language,
            'timeLimit' => $this->timeLimit,
            'memoryLimit' => $this->memoryLimit,
            'fio' => $this->fio,
            'paramSets' => $this->jsonSerializeParamSets($this->paramSets)
        );
    }

    private function jsonSerializeParamSets(array $paramSets){

        $result = array();
        foreach($paramSets as $paramSet){
            $result[] = $paramSet->jsonSerialize();
        }

        return json_encode($result);
    }
}