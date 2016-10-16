<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TestMarkType
 *
 * @ORM\Table(name="test_mark_type", indexes={@ORM\Index(name="test_mark_type_test_id_foreign", columns={"test_id"}), @ORM\Index(name="test_mark_type_mark_type_id_foreign", columns={"mark_type_id"})})
 * @ORM\Entity
 */
class TestMarkType extends BaseEntity
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
     * @var \MarkType
     *
     * @ORM\ManyToOne(targetEntity="MarkType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mark_type_id", referencedColumnName="id")
     * })
     */
    protected $markType;

    /**
     * @var integer
     *
     * @ORM\Column(name="semester", type="smallint", nullable=true)
     */
    protected $semester;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    public function setTest(\Test $test = null)
    {
        $this->test = $test;

        return $this;
    }

    public function getTest()
    {
        return $this->test;
    }

    public function setMarkType(\MarkType $markType = null)
    {
        $this->markType = $markType;

        return $this;
    }

    public function getMarkType()
    {
        return $this->markType;
    }

    public function getSemester()
    {
        return $this->semester;
    }

    public function setSemester($semester)
    {
        $this->semester = $semester;
    }
}

