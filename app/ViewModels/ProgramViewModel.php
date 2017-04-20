<?php


class ProgramViewModel implements JsonSerializable
{
    private $_program;
    private $_paramSets;

    public function ProgramViewModel($program, $paramSets){
        $this->_program = $program;
        $this->_paramSets = $paramSets;
    }

    function jsonSerialize()
    {
        return array(
            'program' => $this->_program,
            'paramSets' => $this->_paramSets
        );
    }

    /**
     * @return mixed
     */
    public function getProgram()
    {
        return $this->_program;
    }

    /**
     * @param mixed $program
     */
    public function setProgram($program)
    {
        $this->_program = $program;
    }

    /**
     * @return mixed
     */
    public function getParamSets()
    {
        return $this->_paramSets;
    }

    /**
     * @param mixed $paramSets
     */
    public function setParamSets($paramSets)
    {
        $this->_paramSets = $paramSets;
    }
}