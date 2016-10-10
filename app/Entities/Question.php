<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question", indexes={@ORM\Index(name="question_theme_id_foreign", columns={"theme_id"})})
 * @ORM\Entity
 */
class Question
{
/**
 * @var boolean
 *
 * @ORM\Column(name="type", type="boolean", nullable=false)
 */
private $type;

/**
 * @var string
 *
 * @ORM\Column(name="text", type="text", length=65535, nullable=false)
 */
private $text;

/**
 * @var string
 *
 * @ORM\Column(name="image", type="string", length=100, nullable=false)
 */
private $image;

/**
 * @var boolean
 *
 * @ORM\Column(name="complexity", type="boolean", nullable=false)
 */
private $complexity;

/**
 * @var integer
 *
 * @ORM\Column(name="time", type="smallint", nullable=false)
 */
private $time;

/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private $id;

/**
 * @var \Theme
 *
 * @ORM\ManyToOne(targetEntity="Theme")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="theme_id", referencedColumnName="id")
 * })
 */
private $theme;


/**
 * Set type
 *
 * @param boolean $type
 *
 * @return Question
 */
public function setType($type)
{
$this->type = $type;

return $this;
}

/**
 * Get type
 *
 * @return boolean
 */
public function getType()
{
return $this->type;
}

/**
 * Set text
 *
 * @param string $text
 *
 * @return Question
 */
public function setText($text)
{
$this->text = $text;

return $this;
}

/**
 * Get text
 *
 * @return string
 */
public function getText()
{
return $this->text;
}

/**
 * Set image
 *
 * @param string $image
 *
 * @return Question
 */
public function setImage($image)
{
$this->image = $image;

return $this;
}

/**
 * Get image
 *
 * @return string
 */
public function getImage()
{
return $this->image;
}

/**
 * Set complexity
 *
 * @param boolean $complexity
 *
 * @return Question
 */
public function setComplexity($complexity)
{
$this->complexity = $complexity;

return $this;
}

/**
 * Get complexity
 *
 * @return boolean
 */
public function getComplexity()
{
return $this->complexity;
}

/**
 * Set time
 *
 * @param integer $time
 *
 * @return Question
 */
public function setTime($time)
{
$this->time = $time;

return $this;
}

/**
 * Get time
 *
 * @return integer
 */
public function getTime()
{
return $this->time;
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
 * Set theme
 *
 * @param \Theme $theme
 *
 * @return Question
 */
public function setTheme(\Theme $theme = null)
{
$this->theme = $theme;

return $this;
}

/**
 * Get theme
 *
 * @return \Theme
 */
public function getTheme()
{
return $this->theme;
}
}

