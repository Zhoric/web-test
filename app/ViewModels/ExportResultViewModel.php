<?php

class ExportResultViewModel implements JsonSerializable
{
    public $totalRows;

    public $exported;

    public $failed;

    public $errors;

    public function __construct()
    {
        $this->totalRows = 0;
        $this->exported = 0;
        $this->failed = 0;
        $this->errors = [];
    }

    function jsonSerialize()
    {
        return array(
            'totalRows' => $this->totalRows,
            'exported' => $this->exported,
            'failed' => $this->failed,
            'errors' => $this->errors
        );
    }
}