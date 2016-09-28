<?php

namespace App\models;

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
      */
    public $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $groupId;

    /**
    * @ORM\Column(type="string", length=20)
    */
    public $login;

    /**
     * @ORM\Column(type="string", length=60)
     */
    public $password;

    /**
     * @ORM\Relation(type="belongsToMany", relatedEntity="App\models\Discipline",
     *     pivotTable="discipline_lecturer", inverse=true)
     */
    public $disciplines;

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
    public $yearShift;

    /**
     * @ORM\Column(type="smallInteger", nullable=true)
     */
    public $admissionYear;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    public $rememberToken;

    public function __construct(){}
}
