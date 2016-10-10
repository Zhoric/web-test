<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Mark
 *
 * @ORM\Table(name="mark", indexes={@ORM\Index(name="mark_mark_type_id_foreign", columns={"mark_type_id"}), @ORM\Index(name="mark_user_id_foreign", columns={"user_id"})})
 * @ORM\Entity
 */
class Mark
{
/**
 * @var boolean
 *
 * @ORM\Column(name="value", type="boolean", nullable=true)
 */
private $value;

/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private $id;

/**
 * @var \MarkType
 *
 * @ORM\ManyToOne(targetEntity="MarkType")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="mark_type_id", referencedColumnName="id")
 * })
 */
private $markType;

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
 * Set value
 *
 * @param boolean $value
 *
 * @return Mark
 */
public function setValue($value)
{
$this->value = $value;

return $this;
}

/**
 * Get value
 *
 * @return boolean
 */
public function getValue()
{
return $this->value;
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
 * @return Mark
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
 * Set user
 *
 * @param \User $user
 *
 * @return Mark
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

