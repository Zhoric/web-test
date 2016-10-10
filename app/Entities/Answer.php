<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="answer", indexes={@ORM\Index(name="answer_question_id_foreign", columns={"question_id"})})
 * @ORM\Entity
 */
class Answer
{
/**
 * @var string
 *
 * @ORM\Column(name="text", type="text", length=65535, nullable=false)
 */
private $text;

/**
 * @var boolean
 *
 * @ORM\Column(name="is_right", type="boolean", nullable=false)
 */
private $isRight;

/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private $id;

/**
 * @var \Question
 *
 * @ORM\ManyToOne(targetEntity="Question")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="question_id", referencedColumnName="id")
 * })
 */
private $question;


/**
 * Set text
 *
 * @param string $text
 *
 * @return Answer
 */
public function setText($text)
{
$this->text = $text;

return $this;
}

/**
 * Get text
 *
 * @return string
 */
public function getText()
{
return $this->text;
}

/**
 * Set isRight
 *
 * @param boolean $isRight
 *
 * @return Answer
 */
public function setIsRight($isRight)
{
$this->isRight = $isRight;

return $this;
}

/**
 * Get isRight
 *
 * @return boolean
 */
public function getIsRight()
{
return $this->isRight;
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
 * @return Answer
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
}

