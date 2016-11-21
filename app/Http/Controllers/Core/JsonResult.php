<?php

/**
 * Модель ответа на JSON-запрос
 */
class JsonResult implements JsonSerializable
{
    /**
     * Данные.
     */
    private $_data;

    /**
     * Признак успещности обработки запроса.
     */
    private $_success;

    /**
     * Сообщение (об ошибке).
     */
    private $_message;

    public function __construct($success, $data = null, $message=null)
    {
        $this->_message = $message;
        $this->_data = $data;
        $this->_success = $success;
    }

    function jsonSerialize()
    {
        return array(
            'Data' => $this->_data,
            'Success' => $this->_success,
            'Message' => $this->_message
        );
    }
}