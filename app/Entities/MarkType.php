<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * MarkType
 *
 * @ORM\Table(name="mark_type", indexes={@ORM\Index(name="mark_type_discipline_plan_id_foreign", columns={"discipline_plan_id"})})
 * @ORM\Entity
 */
class MarkType extends BaseEntity implements JsonSerializable
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
     * @var \DisciplinePlan
     *
     * @ORM\ManyToOne(targetEntity="DisciplinePlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="discipline_plan_id", referencedColumnName="id")
     * })
     */
    protected $disciplinePlan;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return MarkType
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
     * Set disciplinePlan
     *
     * @param \DisciplinePlan $disciplinePlan
     *
     * @return MarkType
     */
    public function setDisciplinePlan(\DisciplinePlan $disciplinePlan = null)
    {
        $this->disciplinePlan = $disciplinePlan;

        return $this;
    }

    /**
     * Get disciplinePlan
     *
     * @return \DisciplinePlan
     */
    public function getDisciplinePlan()
    {
        return $this->disciplinePlan;
    }

    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name
        );
    }
}

