<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Program
 *
 * @ORM\Table(name="program", indexes={@ORM\Index(name="program_question_id_foreign", columns={"question_id"})})
 * @ORM\Entity
 */
class Program extends BaseEntity implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="template", type="text", length=65535, nullable=false)
     */
    protected $template;

    /**
     * @var integer
     *
     * @ORM\Column(name="lang", type="smallint", nullable=false)
     */
    protected $lang;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Question
     *
     * @ORM\ManyToOne(targetEntity="Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    protected $question;


    /**
     * @var integer
     *
     * @ORM\Column(name="memory_limit", type="smallint", nullable=false)
     */
    protected $memoryLimit;

    /**
     * @var \Question
     *
     * @ORM\Column(name="time_limit", type="smallint", nullable=false)
     * })
     */
    protected $timeLimit;

    /**
     * @return Question
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * @param Question $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return int
     */
    public function getMemoryLimit()
    {
        return $this->memoryLimit;
    }

    /**
     * @param int $memoryLimit
     */
    public function setMemoryLimit($memoryLimit)
    {
        $this->memoryLimit = $memoryLimit;
    }




    /**
     * Set template
     *
     * @param string $template
     *
     * @return Program
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set lang
     *
     * @param integer $lang
     *
     * @return Program
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return integer
     */
    public function getLang()
    {
        return $this->lang;
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
     * Set question
     *
     * @param \Question $question
     *
     * @return Program
     */
    public function setQuestion(\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'template' => $this->template,
            'lang' => $this->lang
        );
    }
}

