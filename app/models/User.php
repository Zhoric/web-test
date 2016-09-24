<?php

namespace App\Models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

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
      * @ORM\Relation(type="belongsTo", relatedEntity="App\Models\Group")
      */
    public $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    public $login;

    /**
     * @ORM\Column(type="string", length=60)
     */
    public $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $fullName;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $role;

    /**
     * @ORM\Column(type="smallInteger", nullable=true)
     */
    public $admissionYear;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    public $rememberToken;
}
