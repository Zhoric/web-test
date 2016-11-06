<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TestTheme
 *
 * @ORM\Table(name="test_theme", indexes={@ORM\Index(name="test_theme_test_id_foreign", columns={"test_id"}), @ORM\Index(name="test_theme_theme_id_foreign", columns={"theme_id"})})
 * @ORM\Entity
 */
class TestTheme extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Test
     *
     * @ORM\ManyToOne(targetEntity="Test")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="test_id", referencedColumnName="id")
     * })
     */
    protected $test;

    /**
     * @var \Theme
     *
     * @ORM\ManyToOne(targetEntity="Theme")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="theme_id", referencedColumnName="id")
     * })
     */
    protected $theme;


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
     * Set test
     *
     * @param \Test $test
     *
     * @return TestTheme
     */
    public function setTest(\Test $test = null)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test
     *
     * @return \Test
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Set theme
     *
     * @param \Theme $theme
     *
     * @return TestTheme
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

