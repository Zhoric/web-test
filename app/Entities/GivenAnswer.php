<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * GivenAnswer
 *
 * @ORM\Table(name="given_answer", indexes={@ORM\Index(name="given_answer_question_id_foreign", columns={"question_id"}), @ORM\Index(name="given_answer_test_result_id_foreign", columns={"test_result_id"})})
 * @ORM\Entity
 */
class GivenAnswer extends BaseEntity implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", length=65535, nullable=false)
     */
    protected $answer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="right_percentage", type="smallint", nullable=false)
     */
    protected $rightPercentage;

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
     * @var \TestResult
     *
     * @ORM\ManyToOne(targetEntity="TestResult")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="test_result_id", referencedColumnName="id")
     * })
     */
    protected $testResult;


    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return GivenAnswer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set rightPercentage
     *
     * @param integer $rightPercentage
     *
     * @return GivenAnswer
     */
    public function setRightPercentage($rightPercentage)
    {
        $this->rightPercentage = $rightPercentage;

        return $this;
    }

    /**
     * Get rightPercentage
     *
     * @return integer
     */
    public function getRightPercentage()
    {
        return $this->rightPercentage;
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
     * @return GivenAnswer
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

    /**
     * Set testResult
     *
     * @param \TestResult $testResult
     *
     * @return GivenAnswer
     */
    public function setTestResult(\TestResult $testResult = null)
    {
        $this->testResult = $testResult;
        return $this;
    }

    /**
     * Get testResult
     *
     * @return \TestResult
     */
    public function getTestResult()
    {
        return $this->testResult;
    }

    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'answer' => $this->answer,
            'rightPercentage' => $this->rightPercentage,
            'question' => $this->getQuestion()
        );
    }
}

