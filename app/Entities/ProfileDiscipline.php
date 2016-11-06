<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ProfileDiscipline
 *
 * @ORM\Table(name="profile_discipline", indexes={@ORM\Index(name="profile_discipline_discipline_id_foreign", columns={"discipline_id"}), @ORM\Index(name="profile_discipline_profile_id_foreign", columns={"profile_id"})})
 * @ORM\Entity
 */
class ProfileDiscipline extends BaseEntity
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
     * @var \Discipline
     *
     * @ORM\ManyToOne(targetEntity="Discipline")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
     * })
     */
    protected $discipline;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set discipline
     *
     * @param \Discipline $discipline
     *
     * @return ProfileDiscipline
     */
    public function setDiscipline(\Discipline $discipline = null)
    {
        $this->discipline = $discipline;

        return $this;
    }

    /**
     * Get discipline
     *
     * @return \Discipline
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * Set profile
     *
     * @param \Profile $profile
     *
     * @return ProfileDiscipline
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
}

