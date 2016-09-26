<?php

namespace App\models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="profiles")
 */
class Profile extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Relation(type="belongsToMany", relatedEntity="App\models\Discipline")
     */
    public $disciplines;

    /**
     * @ORM\Relation(type="hasMany", relatedEntity="App\models\Discipline", inverse=true)
     */
    public $groups;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $name;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $semesters;
}
