<?php

class PaginationResult implements JsonSerializable
{
    private $_data;
    private $_count;

    public function __construct($data, $count)
    {
        $this->_data = $data;
        $this->_count = $count;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->_count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->_count = $count;
    }

    function jsonSerialize()
    {
        return array(
            'data' => $this->_data,
            'count' => $this->_count);
    }
}