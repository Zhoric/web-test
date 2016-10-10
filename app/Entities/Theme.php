<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Theme
 *
 * @ORM\Table(name="theme", indexes={@ORM\Index(name="theme_discipline_id_foreign", columns={"discipline_id"})})
 * @ORM\Entity
 */
class Theme
{
/**
 * @var string
 *
 * @ORM\Column(name="name", type="string", length=100, nullable=false)
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
 * @var \Discipline
 *
 * @ORM\ManyToOne(targetEntity="Discipline")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
 * })
 */
private $discipline;


/**
 * Set name
 *
 * @param string $name
 *
 * @return Theme
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
 * Set discipline
 *
 * @param \Discipline $discipline
 *
 * @return Theme
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
}

