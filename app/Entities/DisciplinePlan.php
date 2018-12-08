<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * DisciplinePlan
 *
 * @ORM\Table(name="discipline_plan", indexes={@ORM\Index(name="discipline_plan_discipline_id_foreign", columns={"discipline_id"}), @ORM\Index(name="discipline_plan_studyplan_id_foreign", columns={"studyplan_id"})})
 * @ORM\Entity
 */
class DisciplinePlan extends BaseEntity implements JsonSerializable
{
    /**
     * @var integer
     */
    protected $semester;

    protected $hasExam;
    /**
     * @var boolean
     */
    protected $hasCoursework;

    /**
     * @var boolean
     */
    protected $hasCourseProject;

    /**
     * @var boolean
     */
    protected $hasDesignAssignment;

    /**
     * @var boolean
     */
    protected $hasEssay;

    /**
     * @var boolean
     */
    protected $hasHomeTest;

    /**
     * @var boolean
     */
    protected $hasAudienceTest;

    /**
     * @var integer
     */
    protected $hoursAll;

    /**
     * @var integer
     */
    protected $hoursLecture;

    /**
     * @var integer
     */
    protected $hoursLaboratory;

    /**
     * @var integer
     */
    protected $hoursPractical;

    /**
     * @var integer
     */
    protected $hoursSolo;

    /**
     * @var integer
     */
    protected $countLecture;

    /**
     * @var integer
     */
    protected $countLaboratory;

    /**
     * @var integer
     */
    protected $countPractical;

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
     * @var \Studyplan
     *
     * @ORM\ManyToOne(targetEntity="Studyplan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="studyplan_id", referencedColumnName="id")
     * })
     */
    protected $studyplan;

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'startSemester' => $this->semester,

            'hasExam' => $this->hasExam,
            'hasCourseWork' => $this->hasCoursework,
            'hasCourseProject' => $this->hasCourseProject,
            'hasDesignAssignment' => $this->hasDesignAssignment,
            'hasEssay' => $this->hasEssay,
            'hasHomeTest' => $this->hasHomeTest,
            'hasAudienceTest' => $this->hasAudienceTest,

            'hoursCount' => $this->hoursAll,
            'hoursLecture' => $this->hoursLecture,
            'hoursLaboratory' => $this->hoursLaboratory,
            'hoursPractical' => $this->hoursPractical,
            'hoursSolo' => $this->hoursSolo,

            'lectureCount' => $this->countLecture,
            'laboratoryCount' => $this->countLaboratory,
            'practicalCount' => $this->countPractical,
        );
    }


    /**
     * Set hasExam
     *
     * @param boolean $hasExam
     *
     * @return DisciplinePlan
     */
    public function setHasExam($hasExam)
    {
        $this->hasExam = $hasExam;

        return $this;
    }

    /**
     * Get hasExam
     *
     * @return boolean
     */
    public function getHasExam()
    {
        return $this->hasExam;
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
     * Set discipline
     *
     * @param \Discipline $discipline
     *
     * @return DisciplinePlan
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
     * Set studyplan
     *
     * @param \Studyplan $studyplan
     *
     * @return DisciplinePlan
     */
    public function setStudyplan(\Studyplan $studyplan = null)
    {
        $this->studyplan = $studyplan;

        return $this;
    }

    /**
     * Get studyplan
     *
     * @return \Studyplan
     */
    public function getStudyplan()
    {
        return $this->studyplan;
    }


    /**
     * @return int
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @param int $semester
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;
    }

    /**
     * @return bool
     */
    public function isHasCoursework()
    {
        return $this->hasCoursework;
    }

    /**
     * @param bool $hasCoursework
     */
    public function setHasCoursework($hasCoursework)
    {
        $this->hasCoursework = $hasCoursework;
    }

    /**
     * @return bool
     */
    public function isHasCourseProject()
    {
        return $this->hasCourseProject;
    }

    /**
     * @param bool $hasCourseProject
     */
    public function setHasCourseProject($hasCourseProject)
    {
        $this->hasCourseProject = $hasCourseProject;
    }

    /**
     * @return bool
     */
    public function isHasDesignAssignment()
    {
        return $this->hasDesignAssignment;
    }

    /**
     * @param bool $hasDesignAssignment
     */
    public function setHasDesignAssignment($hasDesignAssignment)
    {
        $this->hasDesignAssignment = $hasDesignAssignment;
    }

    /**
     * @return bool
     */
    public function isHasEssay()
    {
        return $this->hasEssay;
    }

    /**
     * @param bool $hasEssay
     */
    public function setHasEssay($hasEssay)
    {
        $this->hasEssay = $hasEssay;
    }

    /**
     * @return bool
     */
    public function isHasHomeTest()
    {
        return $this->hasHomeTest;
    }

    /**
     * @param bool $hasHomeTest
     */
    public function setHasHomeTest($hasHomeTest)
    {
        $this->hasHomeTest = $hasHomeTest;
    }

    /**
     * @return bool
     */
    public function isHasAudienceTest()
    {
        return $this->hasAudienceTest;
    }

    /**
     * @param bool $hasAudienceTest
     */
    public function setHasAudienceTest($hasAudienceTest)
    {
        $this->hasAudienceTest = $hasAudienceTest;
    }

    /**
     * @return int
     */
    public function getHoursAll()
    {
        return $this->hoursAll;
    }

    /**
     * @param int $hoursAll
     */
    public function setHoursAll($hoursAll)
    {
        $this->hoursAll = $hoursAll;
    }

    /**
     * @return int
     */
    public function getHoursLecture()
    {
        return $this->hoursLecture;
    }

    /**
     * @param int $hoursLecture
     */
    public function setHoursLecture($hoursLecture)
    {
        $this->hoursLecture = $hoursLecture;
    }

    /**
     * @return int
     */
    public function getHoursPractical()
    {
        return $this->hoursPractical;
    }

    /**
     * @param int $hoursPractical
     */
    public function setHoursPractical($hoursPractical)
    {
        $this->hoursPractical = $hoursPractical;
    }

    /**
     * @return int
     */
    public function getHoursLaboratory()
    {
        return $this->hoursLaboratory;
    }

    /**
     * @param int $hoursLaboratory
     */
    public function setHoursLaboratory($hoursLaboratory)
    {
        $this->hoursLaboratory = $hoursLaboratory;
    }

    /**
     * @return int
     */
    public function getHoursSolo()
    {
        return $this->hoursSolo;
    }

    /**
     * @param int $hoursSolo
     */
    public function setHoursSolo($hoursSolo)
    {
        $this->hoursSolo = $hoursSolo;
    }

    /**
     * @return int
     */
    public function getCountLecture()
    {
        return $this->countLecture;
    }

    /**
     * @param int $countLecture
     */
    public function setCountLecture($countLecture)
    {
        $this->countLecture = $countLecture;
    }

    /**
     * @return int
     */
    public function getCountLaboratory()
    {
        return $this->countLaboratory;
    }

    /**
     * @param int $countLaboratory
     */
    public function setCountLaboratory($countLaboratory)
    {
        $this->countLaboratory = $countLaboratory;
    }

    /**
     * @return int
     */
    public function getCountPractical()
    {
        return $this->countPractical;
    }

    /**
     * @param int $countPractical
     */
    public function setCountPractical($countPractical)
    {
        $this->countPractical = $countPractical;
    }
}

