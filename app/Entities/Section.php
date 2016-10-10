<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Section
 *
 * @ORM\Table(name="section", indexes={@ORM\Index(name="section_theme_id_foreign", columns={"theme_id"})})
 * @ORM\Entity
 */
class Section
{
/**
 * @var string
 *
 * @ORM\Column(name="name", type="string", length=200, nullable=false)
 */
private $name;

/**
 * @var string
 *
 * @ORM\Column(name="content", type="text", length=65535, nullable=true)
 */
private $content;

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
 * Set name
 *
 * @param string $name
 *
 * @return Section
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
 * Set content
 *
 * @param string $content
 *
 * @return Section
 */
public function setContent($content)
{
$this->content = $content;

return $this;
}

/**
 * Get content
 *
 * @return string
 */
public function getContent()
{
return $this->content;
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
 * @return Section
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

