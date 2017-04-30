<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="docker_info")
 * @ORM\Entity
 */
class DockerInfo extends BaseEntity
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="container_id", type="string", length=300, nullable=false)
     */
    protected $containerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="lang", type="smallint", nullable=false)
     */
    protected $lang;


    /**
     * @return string
     */
    public function getContainerId()
    {
        return $this->containerId;
    }

    /**
     * @param string $containerId
     */
    public function setContainerId($containerId)
    {
        $this->containerId = $containerId;
    }

    /**
     * @return int
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param int $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }



}