<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TestResult
 *
 * @ORM\Table(name="test_result", indexes={@ORM\Index(name="test_result_user_id_foreign", columns={"user_id"}), @ORM\Index(name="test_result_test_id_foreign", columns={"test_id"}), @ORM\Index(name="test_result_mark_type_id_foreign", columns={"mark_type_id"})})
 * @ORM\Entity
 */
class TestResult extends BaseEntity implements JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="attempt", type="smallint", nullable=true)
     */
    protected $attempt;

    /**
     * @var integer
     *
     * @ORM\Column(name="mark", type="smallint", nullable=true)
     */
    protected $mark;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time", type="datetime", nullable=false)
     */
    protected $dateTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \MarkType
     *
     * @ORM\ManyToOne(targetEntity="MarkType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mark_type_id", referencedColumnName="id")
     * })
     */
    protected $markType;

    /**
     * @var \Test
     *
     * @ORM\ManyToOne(targetEntity="Test")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="test_id", referencedColumnName="id")
     * })
     */
    protected $test;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    protected $user;


    /**
     * Set attempt
     *
     * @param integer $attempt
     *
     * @return TestResult
     */
    public function setAttempt($attempt)
    {
        $this->attempt = $attempt;

        return $this;
    }

    /**
     * Get attempt
     *
     * @return integer
     */
    public function getAttempt()
    {
        return $this->attempt;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return TestResult
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
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
     * Set markType
     *
     * @param \MarkType $markType
     *
     * @return TestResult
     */
    public function setMarkType(\MarkType $markType = null)
    {
        $this->markType = $markType;

        return $this;
    }

    /**
     * Get markType
     *
     * @return \MarkType
     */
    public function getMarkType()
    {
        return $this->markType;
    }

    /**
     * Set test
     *
     * @param \Test $test
     *
     * @return TestResult
     */
    public function setTest(\Test $test = null)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test
     *
     * @return \Test
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Set user
     *
     * @param \User $user
     *
     * @return TestResult
     */
    public function setUser(\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \User
     */
    public function getUser()
    {
        return $this->user;
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

    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'mark' => $this->mark,
            'userId' => $this->getUser()->getId(),
            'user' => $this->getUser(),
            'testId' => $this->getTest()->getId(),
            'attempt' => $this->getAttempt(),
            'dateTime' => $this->getDateTime());
    }
}

