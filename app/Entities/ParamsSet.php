<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ParamsSet
 *
 * @ORM\Table(name="params_set", indexes={@ORM\Index(name="params_set_program_id_foreign", columns={"program_id"})})
 * @ORM\Entity
 */
class ParamsSet
{
/**
 * @var string
 *
 * @ORM\Column(name="expected_output", type="string", length=100, nullable=false)
 */
private $expectedOutput;

/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private $id;

/**
 * @var \Program
 *
 * @ORM\ManyToOne(targetEntity="Program")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="program_id", referencedColumnName="id")
 * })
 */
private $program;


/**
 * Set expectedOutput
 *
 * @param string $expectedOutput
 *
 * @return ParamsSet
 */
public function setExpectedOutput($expectedOutput)
{
$this->expectedOutput = $expectedOutput;

return $this;
}

/**
 * Get expectedOutput
 *
 * @return string
 */
public function getExpectedOutput()
{
return $this->expectedOutput;
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
 * Set program
 *
 * @param \Program $program
 *
 * @return ParamsSet
 */
public function setProgram(\Program $program = null)
{
$this->program = $program;

return $this;
}

/**
 * Get program
 *
 * @return \Program
 */
public function getProgram()
{
return $this->program;
}
}

