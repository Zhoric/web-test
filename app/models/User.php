<?php

namespace App\models;

use Illuminate\Contracts\Auth\Authenticatable;
use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\EntityManager;
use ProAI\Datamapper\Support\Entity;


/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends Entity implements Authenticatable
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


    /**
     * @return string
     */
    public function getAuthIdentifierName()
    {
        // Return the name of unique identifier for the user (e.g. "id")
        return "id";
    }

    /**
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        // Return the unique identifier for the user (e.g. their ID, 123)
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAuthPassword()
    {
        // Returns the (hashed) password for the user
        return $this->password;
    }

    /**
     * @return string
     */
    public function getRememberToken()
    {
        // Return the token used for the "remember me" functionality
        return $this->rememberToken;
    }

    /**
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Store a new token user for the "remember me" functionality
        $crypted = bcrypt($value);
        $this->rememberToken = $crypted;
        $em = new EntityManager();
        $em->update($this);
    }

    /**
     * @return string
     */
    public function getRememberTokenName()
    {
        // Return the name of the column / attribute used to store the "remember me" token
        return "remember_token";

    }

}
