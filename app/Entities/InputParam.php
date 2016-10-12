<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * InputParam
 *
 * @ORM\Table(name="input_param", indexes={@ORM\Index(name="input_param_params_set_id_foreign", columns={"params_set_id"})})
 * @ORM\Entity
 */
class InputParam extends BaseEntity implements JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer", nullable=false)
     */
    protected $number;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100, nullable=false)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=100, nullable=false)
     */
    protected $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \ParamsSet
     *
     * @ORM\ManyToOne(targetEntity="ParamsSet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="params_set_id", referencedColumnName="id")
     * })
     */
    protected $paramsSet;


    /**
     * Set number
     *
     * @param integer $number
     *
     * @return InputParam
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return InputParam
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return InputParam
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
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
     * Set paramsSet
     *
     * @param \ParamsSet $paramsSet
     *
     * @return InputParam
     */
    public function setParamsSet(\ParamsSet $paramsSet = null)
    {
        $this->paramsSet = $paramsSet;

        return $this;
    }

    /**
     * Get paramsSet
     *
     * @return \ParamsSet
     */
    public function getParamsSet()
    {
        return $this->paramsSet;
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

