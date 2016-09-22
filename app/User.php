<?php

namespace App;

use ProAI\Datamapper\Support\Entity;
use ProAI\Datamapper\Annotations as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $login;

    /**
     * @ORM\Column(type="string", length=60)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $fullName;

    /**
     * @ORM\Column(type="smallInteger")
     */
    protected $role;

    /**
     * @ORM\Column(type="smallInteger", nullable=true)
     */
    protected $admissionYear;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $rememberToken;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $groupId;
    //     * @Relation(type="belongsTo", relatedEntity="app\Group")


}
