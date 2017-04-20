<?php

class DisciplineInfoViewModel implements JsonSerializable
{
    protected $discipline;

    protected $testsCount;

    protected $testsPassed;

    public function __construct($discipline, $count, $passed)
    {
        $this->discipline = $discipline;
        $this->testsCount = $count;
        $this->testsPassed = $passed;
    }

    function jsonSerialize()
    {
        return array(
            'discipline' => $this->discipline,
            'testsCount' => $this->testsCount,
            'testsPassed' => $this->testsPassed
        );
    }
}