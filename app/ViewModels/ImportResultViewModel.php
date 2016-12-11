<?php

class ImportResultViewModel implements JsonSerializable
{
    public $totalRows;

    public $imported;

    public $failed;

    public $errors;

    public function __construct()
    {
        $this->totalRows = 0;
        $this->imported = 0;
        $this->failed = 0;
        $this->errors = [];
    }

    function jsonSerialize()
    {
        return array(
            'totalRows' => $this->totalRows,
            'exported' => $this->imported,
            'failed' => $this->failed,
            'errors' => $this->errors
        );
    }
}