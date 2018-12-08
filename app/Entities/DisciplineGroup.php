    <?php



use Doctrine\ORM\Mapping as ORM;

/**
 * DisciplineGroup
 *
 * @ORM\Table(name="discipline_group", indexes={@ORM\Index(name="discipline_group_group_id_foreign", columns={"group_id"}),{@ORM\Index(name="discipline_group_discipline_id_foreign", columns={"discipline_id"}), @ORM\Index(name="discipline_group_studyplan_id_foreign", columns={"studyplan_id"})})
 * @ORM\Entity
 */
class DisciplineGroup extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    protected $name;
    protected $abbreviation;
    protected $description;
    /**
     * @var \Group
     *
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    protected $group;

    /**
     * @var \Studyplan
     *
     * @ORM\ManyToOne(targetEntity="Studyplan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="studyplan_id", referencedColumnName="id")
     * })
     */
    protected $studyplan;

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
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return Studyplan
     */
    public function getStudyplan()
    {
        return $this->studyplan;
    }

    /**
     * @param Studyplan $studyplan
     */
    public function setStudyplan($studyplan)
    {
        $this->studyplan = $studyplan;
    }

    /**
     * @return Discipline
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * @param Discipline $discipline
     */
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;
    }

}

