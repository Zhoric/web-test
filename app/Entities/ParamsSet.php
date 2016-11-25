<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ParamsSet
 *
 * @ORM\Table(name="params_set", indexes={@ORM\Index(name="params_set_program_id_foreign", columns={"program_id"})})
 * @ORM\Entity
 */
class ParamsSet extends BaseEntity implements JsonSerializable
{

    /**
     * @var string
     *
     * @ORM\Column(name="input", type="text", length=65535, nullable=false)
     */
    protected $input;
    /**
     * @var string
     *
     * @ORM\Column(name="expected_output", type="text", length=65535, nullable=false)
     */
    protected $expectedOutput;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * })
     */
    protected $program;


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


    function jsonSerialize()
    {
        return array('id' => $this->id,
                     'input' => $this->input,
                     'expectedOutput' => $this->expectedOutput);
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param string $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }
}

