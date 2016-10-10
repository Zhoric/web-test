<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="patronymic", type="string", length=255, nullable=true)
     */
    private $patronymic;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60, nullable=true)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="remember_token", type="string", length=100, nullable=true)
     */
    private $rememberToken;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
    $this->firstname = $firstname;

    return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
    return $this->firstname;
    }

    /**
     * Set patronymic
     *
     * @param string $patronymic
     *
     * @return User
     */
    public function setPatronymic($patronymic)
    {
    $this->patronymic = $patronymic;

    return $this;
    }

    /**
     * Get patronymic
     *
     * @return string
     */
    public function getPatronymic()
    {
    return $this->patronymic;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
    $this->lastname = $lastname;

    return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
    return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
    $this->email = $email;

    return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
    return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
    $this->password = $password;

    return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
    return $this->password;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return User
     */
    public function setActive($active)
    {
    $this->active = $active;

    return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
    return $this->active;
    }

    /**
     * Set rememberToken
     *
     * @param string $rememberToken
     *
     * @return User
     */
    public function setRememberToken($rememberToken)
    {
    $this->rememberToken = $rememberToken;

    return $this;
    }

    /**
     * Get rememberToken
     *
     * @return string
     */
    public function getRememberToken()
    {
    return $this->rememberToken;
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

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'firstName' => $this->firstname,
            'patronymic'=> $this->patronymic,
            'lastName' => $this->lastname,
            'email' => $this->email,
            'active' => $this->active,
        );
    }
}

