<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * StudentGroup
 *
 * @ORM\Table(name="student_group", indexes={@ORM\Index(name="student_group_group_id_foreign", columns={"group_id"}), @ORM\Index(name="student_group_student_id_foreign", columns={"student_id"})})
 * @ORM\Entity
 */
class StudentGroup
{
/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private $id;

/**
 * @var \Group
 *
 * @ORM\ManyToOne(targetEntity="Group")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
 * })
 */
private $group;

/**
 * @var \User
 *
 * @ORM\ManyToOne(targetEntity="User")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="student_id", referencedColumnName="id")
 * })
 */
private $student;


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
 * Set group
 *
 * @param \Group $group
 *
 * @return StudentGroup
 */
public function setGroup(\Group $group = null)
{
$this->group = $group;

return $this;
}

/**
 * Get group
 *
 * @return \Group
 */
public function getGroup()
{
return $this->group;
}

/**
 * Set student
 *
 * @param \User $student
 *
 * @return StudentGroup
 */
public function setStudent(\User $student = null)
{
$this->student = $student;

return $this;
}

/**
 * Get student
 *
 * @return \User
 */
public function getStudent()
{
return $this->student;
}
}

