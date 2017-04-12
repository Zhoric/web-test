<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Test
 *
 * @ORM\Table(name="test", indexes={@ORM\Index(name="test_discipline_id_foreign", columns={"discipline_id"})})
 * @ORM\Entity
 */
class Test extends BaseEntity implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=200, nullable=true)
     */
    protected $subject;

    /**
     * @var integer
     *
     * @ORM\Column(name="time_total", type="smallint", nullable=true)
     */
    protected $timeTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="attempts", type="smallint", nullable=true)
     */
    protected $attempts;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    protected $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    protected $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_random", type="boolean", nullable=true)
     */
    protected $isRandom;

    /**
     * @var \Discipline
     *
     * @ORM\ManyToOne(targetEntity="Discipline")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
     * })
     */
    protected $discipline;


    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Test
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set timeTotal
     *
     * @param integer $timeTotal
     *
     * @return Test
     */
    public function setTimeTotal($timeTotal)
    {
        $this->timeTotal = $timeTotal;

        return $this;
    }

    /**
     * Get timeTotal
     *
     * @return integer
     */
    public function getTimeTotal()
    {
        return $this->timeTotal;
    }

    /**
     * Set attempts
     *
     * @param integer $attempts
     *
     * @return Test
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Get attempts
     *
     * @return integer
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set discipline
     *
     * @param \Discipline $discipline
     *
     * @return Test
     */
    public function setDiscipline(\Discipline $discipline = null)
    {
        $this->discipline = $discipline;

        return $this;
    }

    /**
     * Get discipline
     *
     * @return \Discipline
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }


    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'subject' => $this->subject,
            'timeTotal' => $this->timeTotal,
            'type' => $this->type,
            'isActive' => $this->isActive,
            'attempts' => $this->attempts,
            'isRandom' => $this->isRandom,
            'disciplineId' => $this->discipline->getId(),
            'disciplineName' => $this->discipline->getName()
        );
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public function IsActive()
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return boolean
     */
    public function isIsRandom()
    {
        return $this->isRandom;
    }

    /**
     * @param boolean $isRandom
     */
    public function setIsRandom($isRandom)
    {
        $this->isRandom = $isRandom;
    }

    /**
     * @param int $id
     * @return Test
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}

