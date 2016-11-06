<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ExtraAttempt
 *
 * @ORM\Table(name="extra_attempt", indexes={@ORM\Index(name="extra_attempt_test_id_foreign", columns={"test_id"}), @ORM\Index(name="extra_attempt_user_id_foreign", columns={"user_id"})})
 * @ORM\Entity
 */
class ExtraAttempt extends BaseEntity implements JsonSerializable
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="count", type="smallint", nullable=true)
     */
    protected $count;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

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
     * Set count
     *
     * @param integer $count
     *
     * @return ExtraAttempt
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
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
     * Set test
     *
     * @param \Test $test
     *
     * @return ExtraAttempt
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
     * @return ExtraAttempt
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

