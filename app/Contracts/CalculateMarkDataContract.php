<?php


class CalculateMarkDataContract extends BaseContract implements JsonSerializable
{

    /**
     * @var int id ответа на вопрос (cущность бд)
     */
    private $givenAnswerId;

    /**
     * @return int
     */
    public function getGivenAnswerId()
    {
        return $this->givenAnswerId;
    }

    /**
     * @param int $givenAnswerId
     */
    public function setGivenAnswerId($givenAnswerId)
    {
        $this->givenAnswerId = $givenAnswerId;
    }

    /**
     * @return int
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * @param int $mark
     */
    public function setMark($mark)
    {
        $this->mark = $mark;
    }

    /**
     * @var int оцена от 0 до 100 за тест
     */
    private $mark;

    public function jsonSerialize()
    {
        return array(
            'givenAnswerId' => $this->givenAnswerId,
            'mark' => $this->mark,
        );
    }
}