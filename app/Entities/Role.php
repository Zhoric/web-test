<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity
 */
class Role
{
/**
 * @var string
 *
 * @ORM\Column(name="name", type="string", length=100, nullable=false)
 */
private $name;

/**
 * @var string
 *
 * @ORM\Column(name="description", type="text", length=65535, nullable=true)
 */
private $description;

/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private $id;


/**
 * Set name
 *
 * @param string $name
 *
 * @return Role
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
 * Set description
 *
 * @param string $description
 *
 * @return Role
 */
public function setDescription($description)
{
$this->description = $description;

return $this;
}

/**
 * Get description
 *
 * @return string
 */
public function getDescription()
{
return $this->description;
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
}

