<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * DisciplineLecturer
 *
 * @ORM\Table(name="discipline_lecturer", indexes={@ORM\Index(name="discipline_lecturer_discipline_id_foreign", columns={"discipline_id"}), @ORM\Index(name="discipline_lecturer_lecturer_id_foreign", columns={"lecturer_id"})})
 * @ORM\Entity
 */
class DisciplineLecturer
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
 * @var \Discipline
 *
 * @ORM\ManyToOne(targetEntity="Discipline")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
 * })
 */
private $discipline;

/**
 * @var \User
 *
 * @ORM\ManyToOne(targetEntity="User")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="lecturer_id", referencedColumnName="id")
 * })
 */
private $lecturer;


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
 * @return DisciplineLecturer
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
 * Set lecturer
 *
 * @param \User $lecturer
 *
 * @return DisciplineLecturer
 */
public function setLecturer(\User $lecturer = null)
{
$this->lecturer = $lecturer;

return $this;
}

/**
 * Get lecturer
 *
 * @return \User
 */
public function getLecturer()
{
return $this->lecturer;
}
}

