<?php

class LecturerInfoViewModel implements JsonSerializable
{
    protected $lecturer;

    protected $disciplines;

    public function __construct($lecturer, $disciplines)
    {
        $this->lecturer = $lecturer;
        $this->disciplines = $disciplines;
    }

    function jsonSerialize()
    {
        return array(
            'lecturer' => $this->lecturer,
            'disciplines' => $this->disciplines
        );
    }
}