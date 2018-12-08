<?php



/**
 * StudentAttendance
 */
class StudentAttendance
{
    /**
     * @var integer
     */
    private $occupationType;

    /**
     * @var integer
     */
    private $occupationNumber;

    /**
     * @var boolean
     */
    private $visitStatus;

    private $disciplineGroup;

    private $studentGroup;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \User
     */
    private $student;

    /**
     * @var \DisciplineGroup
     */
    private $discipline_group;

    /**
     * @return int
     */
    public function getOccupationType()
    {
        return $this->occupationType;
    }

    /**
     * @param int $occupationType
     */
    public function setOccupationType($occupationType)
    {
        $this->occupationType = $occupationType;
    }

    /**
     * @return int
     */
    public function getOccupationNumber()
    {
        return $this->occupationNumber;
    }

    /**
     * @param int $occupationNumber
     */
    public function setOccupationNumber($occupationNumber)
    {
        $this->occupationNumber = $occupationNumber;
    }

    /**
     * @return bool
     */
    public function isWasVisited()
    {
        return $this->wasVisited;
    }

    /**
     * @param bool $wasVisited
     */
    public function setWasVisited($wasVisited)
    {
        $this->wasVisited = $wasVisited;
    }

    /**
     * @return User
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param User $student
     */
    public function setStudent($student)
    {
        $this->student = $student;
    }

    /**
     * @return DisciplineGroup
     */
    public function getDisciplineGroup()
    {
        return $this->discipline_group;
    }

    /**
     * @param DisciplineGroup $discipline_group
     */
    public function setDisciplineGroup($discipline_group)
    {
        $this->discipline_group = $discipline_group;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


}

