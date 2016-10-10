<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * InputParam
 *
 * @ORM\Table(name="input_param", indexes={@ORM\Index(name="input_param_params_set_id_foreign", columns={"params_set_id"})})
 * @ORM\Entity
 */
class InputParam
{
/**
 * @var integer
 *
 * @ORM\Column(name="number", type="integer", nullable=false)
 */
private $number;

/**
 * @var string
 *
 * @ORM\Column(name="type", type="string", length=100, nullable=false)
 */
private $type;

/**
 * @var string
 *
 * @ORM\Column(name="value", type="string", length=100, nullable=false)
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
 * @var \ParamsSet
 *
 * @ORM\ManyToOne(targetEntity="ParamsSet")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="params_set_id", referencedColumnName="id")
 * })
 */
private $paramsSet;


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
}

