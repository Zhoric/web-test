<?php

namespace App\Models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;


/**
 * @ORM\Entity
 * @ORM\Table(name="groups")
 */
class Group extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     * @ORM\Relation(type="belongsTo", relatedEntity="App\models\Profile")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    public $prefix;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $studyYear;

    /**
     * @ORM\Column(type="boolean")
     */
    public $isFullTime;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $number;

    /**
     * @ORM\Column(type="string", length=20)
     */
    public $name;
}
