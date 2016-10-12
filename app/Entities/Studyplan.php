<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Studyplan
 *
 * @ORM\Table(name="studyplan", indexes={@ORM\Index(name="studyplan_profile_id_foreign", columns={"profile_id"})})
 * @ORM\Entity
 */
class Studyplan extends BaseEntity implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Profile
     *
     * @ORM\ManyToOne(targetEntity="Profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     * })
     */
    protected $profile;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Studyplan
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set profile
     *
     * @param \Profile $profile
     *
     * @return Studyplan
     */
    public function setProfile(\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'profileId' => $this->profile->getId(),
        );
    }
}

