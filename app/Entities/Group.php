<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="group", indexes={@ORM\Index(name="group_studyplan_id_foreign", columns={"studyplan_id"})})
 * @ORM\Entity
 */
class Group
{
/**
 * @var string
 *
 * @ORM\Column(name="prefix", type="string", length=50, nullable=true)
 */
private $prefix;

/**
 * @var integer
 *
 * @ORM\Column(name="course", type="smallint", nullable=true)
 */
private $course;

/**
 * @var boolean
 *
 * @ORM\Column(name="number", type="boolean", nullable=false)
 */
private $number;

/**
 * @var boolean
 *
 * @ORM\Column(name="is_fulltime", type="boolean", nullable=false)
 */
private $isFulltime;

/**
 * @var string
 *
 * @ORM\Column(name="name", type="string", length=100, nullable=true)
 */
private $name;

/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private $id;

/**
 * @var \Studyplan
 *
 * @ORM\ManyToOne(targetEntity="Studyplan")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="studyplan_id", referencedColumnName="id")
 * })
 */
private $studyplan;


/**
 * Set prefix
 *
 * @param string $prefix
 *
 * @return Group
 */
public function setPrefix($prefix)
{
$this->prefix = $prefix;

return $this;
}

/**
 * Get prefix
 *
 * @return string
 */
public function getPrefix()
{
return $this->prefix;
}

/**
 * Set course
 *
 * @param integer $course
 *
 * @return Group
 */
public function setCourse($course)
{
$this->course = $course;

return $this;
}

/**
 * Get course
 *
 * @return integer
 */
public function getCourse()
{
return $this->course;
}

/**
 * Set number
 *
 * @param boolean $number
 *
 * @return Group
 */
public function setNumber($number)
{
$this->number = $number;

return $this;
}

/**
 * Get number
 *
 * @return boolean
 */
public function getNumber()
{
return $this->number;
}

/**
 * Set isFulltime
 *
 * @param boolean $isFulltime
 *
 * @return Group
 */
public function setIsFulltime($isFulltime)
{
$this->isFulltime = $isFulltime;

return $this;
}

/**
 * Get isFulltime
 *
 * @return boolean
 */
public function getIsFulltime()
{
return $this->isFulltime;
}

/**
 * Set name
 *
 * @param string $name
 *
 * @return Group
 */
public function setName($name)
{
$this->name = $name;

return $this;
}

/**
 * Get name
 *
 * @return string
 */
public function getName()
{
return $this->name;
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
 * Set studyplan
 *
 * @param \Studyplan $studyplan
 *
 * @return Group
 */
public function setStudyplan(\Studyplan $studyplan = null)
{
$this->studyplan = $studyplan;

return $this;
}

/**
 * Get studyplan
 *
 * @return \Studyplan
 */
public function getStudyplan()
{
return $this->studyplan;
}
}

