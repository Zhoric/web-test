<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 *
 * @ORM\Table(name="profile", indexes={@ORM\Index(name="profile_institute_id_foreign", columns={"institute_id"})})
 * @ORM\Entity
 */
class Profile extends BaseEntity implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=255, nullable=true)
     */
    protected $fullname;

    /**
     * @var integer
     *
     * @ORM\Column(name="semesters", type="smallint", nullable=true)
     */
    protected $semesters;

    /**
     * @var integerhasProjectWork
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Institute
     *
     * @ORM\ManyToOne(targetEntity="Institute")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institute_id", referencedColumnName="id")
     * })
     */
    protected $institute;


    /**
     * Set code
     *
     * @param string $code
     *
     * @return Profile
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Profile
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
     * Set fullname
     *
     * @param string $fullname
     *
     * @return Profile
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set semesters
     *
     * @param smallint $semesters
     *
     * @return Profile
     */
    public function setSemesters($semesters)
    {
        $this->semesters = $semesters;

        return $this;
    }

    /**
     * Get semesters
     *
     * @return smallint
     */
    public function getSemesters()
    {
        return $this->semesters;
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
     * Set institute
     *
     * @param \Institute $institute
     *
     * @return Profile
     */
    public function setInstitute(\Institute $institute = null)
    {
        $this->institute = $institute;

        return $this;
    }

    /**
     * Get institute
     *
     * @return \Institute
     */
    public function getInstitute()
    {
        return $this->institute;
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'fullname' => $this->fullname,
            'code' => $this->code,
            'semesters' => $this->semesters
        );
    }
}

