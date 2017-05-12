<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Mediable
 *
 * @ORM\Table(name="mediable", indexes={@ORM\Index(name="mediable_media_id_foreign", columns={"media_id"}),
 *     @ORM\Index(name="mediable_theme_id_foreign", columns={"theme_id"}),
 *     @ORM\Index(name="mediable_discipline_id_foreign", columns={"discipline_id"})})
 * @ORM\Entity
 */
class Mediable extends BaseEntity implements JsonSerializable
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
     * @var \Media
     *
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * })
     */
    protected $media;

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
     * @var \Discipline
     *
     * @ORM\ManyToOne(targetEntity="Discipline")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
     * })
     */
    protected $discipline;

    /**
     * @var string
     *
     * @ORM\Column(name="start", type="string", , length=255, nullable=true)
     */
    protected $start;

    /**
     * @var string
     *
     * @ORM\Column(name="stop", type="string", , length=255, nullable=true)
     */
    protected $stop;



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
     * Get media
     *
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set media
     *
     * @param Media $media
     */

    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * Set theme
     *
     * @param \Theme $theme
     *
     * @return Mediable
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

    /**
     * @return \Discipline
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * @param \Discipline $discipline
     *
     * @return Mediable
     */

    public function setDiscipline(\Discipline $discipline = null)
    {
        $this->discipline = $discipline;

        return $this;
    }


    /**
     * Set start
     *
     * @param string $start
     *
     * @return Mediable
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return string
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set stop
     *
     * @param string $stop
     *
     * @return Mediable
     */
    public function setStop($stop)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Get stop
     *
     * @return string
     */
    public function getStop()
    {
        return $this->stop;
    }



    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'start' => $this->start,
            'stop' => $this->stop,
            'media' => $this->media,
            'theme' => $this->theme,
            'discipline' => $this->discipline
        );
    }
}

