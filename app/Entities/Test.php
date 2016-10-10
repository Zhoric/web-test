<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Test
 *
 * @ORM\Table(name="test", indexes={@ORM\Index(name="test_discipline_id_foreign", columns={"discipline_id"})})
 * @ORM\Entity
 */
class Test
{
/**
 * @var string
 *
 * @ORM\Column(name="subject", type="string", length=200, nullable=true)
 */
private $subject;

/**
 * @var integer
 *
 * @ORM\Column(name="time_total", type="smallint", nullable=true)
 */
private $timeTotal;

/**
 * @var boolean
 *
 * @ORM\Column(name="attempts", type="boolean", nullable=true)
 */
private $attempts;

/**
 * @var boolean
 *
 * @ORM\Column(name="order_number", type="boolean", nullable=true)
 */
private $orderNumber;

/**
 * @var boolean
 *
 * @ORM\Column(name="fulltime_start", type="boolean", nullable=true)
 */
private $fulltimeStart;

/**
 * @var boolean
 *
 * @ORM\Column(name="extramural_start", type="boolean", nullable=true)
 */
private $extramuralStart;

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
 * @param boolean $attempts
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
 * @return boolean
 */
public function getAttempts()
{
return $this->attempts;
}

/**
 * Set orderNumber
 *
 * @param boolean $orderNumber
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
 * @return boolean
 */
public function getOrderNumber()
{
return $this->orderNumber;
}

/**
 * Set fulltimeStart
 *
 * @param boolean $fulltimeStart
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
 * @return boolean
 */
public function getFulltimeStart()
{
return $this->fulltimeStart;
}

/**
 * Set extramuralStart
 *
 * @param boolean $extramuralStart
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
 * @return boolean
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
}

