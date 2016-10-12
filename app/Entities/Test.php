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
     * @ORM\Column(name="order_number", type="smallint", nullable=true)
     */
    protected $orderNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="fulltime_start", type="smallint", nullable=true)
     */
    protected $fulltimeStart;

    /**
     * @var integer
     *
     * @ORM\Column(name="extramural_start", type="smallint", nullable=true)
     */
    protected $extramuralStart;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

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
     * Set orderNumber
     *
     * @param integer $orderNumber
     *
     * @return Test
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return integer
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set fulltimeStart
     *
     * @param integer $fulltimeStart
     *
     * @return Test
     */
    public function setFulltimeStart($fulltimeStart)
    {
        $this->fulltimeStart = $fulltimeStart;

        return $this;
    }

    /**
     * Get fulltimeStart
     *
     * @return integer
     */
    public function getFulltimeStart()
    {
        return $this->fulltimeStart;
    }

    /**
     * Set extramuralStart
     *
     * @param integer $extramuralStart
     *
     * @return Test
     */
    public function setExtramuralStart($extramuralStart)
    {
        $this->extramuralStart = $extramuralStart;

        return $this;
    }

    /**
     * Get extramuralStart
     *
     * @return integer
     */
    public function getExtramuralStart()
    {
        return $this->extramuralStart;
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}

