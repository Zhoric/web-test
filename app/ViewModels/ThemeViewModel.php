<?php

class ThemeViewModel implements JsonSerializable
{
    public $id;
    public $name;
    public $disciplineId;
    public $questionsCount;
    public $totalTimeInSeconds;

    public function __construct($id, $name, $disciplineId, $questionsCount, $totalTimeInSeconds)
    {
        $this->id = $id;
        $this->name = $name;
        $this->disciplineId = $disciplineId;
        $this->questionsCount = $questionsCount;
        $this->totalTimeInSeconds = $totalTimeInSeconds;
    }

    function jsonSerialize() {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "discipline" => $this->disciplineId,
            "questionsCount" => $this->questionsCount,
            "totalTimeInSeconds" => $this->totalTimeInSeconds
        ];
    }
}