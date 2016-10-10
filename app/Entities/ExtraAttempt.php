<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ExtraAttempt
 *
 * @ORM\Table(name="extra_attempt", indexes={@ORM\Index(name="extra_attempt_test_id_foreign", columns={"test_id"}), @ORM\Index(name="extra_attempt_user_id_foreign", columns={"user_id"})})
 * @ORM\Entity
 */
class ExtraAttempt
{
/**
 * @var boolean
 *
 * @ORM\Column(name="count", type="boolean", nullable=true)
 */
private $count;

/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private $id;

/**
 * @var \Test
 *
 * @ORM\ManyToOne(targetEntity="Test")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="test_id", referencedColumnName="id")
 * })
 */
private $test;

/**
 * @var \User
 *
 * @ORM\ManyToOne(targetEntity="User")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
 * })
 */
private $user;


/**
 * Set count
 *
 * @param boolean $count
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
 * @return boolean
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
}

