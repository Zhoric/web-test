<?php

namespace App\models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="disciplines")
 */
class Discipline extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Relation(type="belongsToMany", relatedEntity="App\models\User", pivotTable="discipline_lecturer")
     */
    public $lecturers;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $name;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $fullTimeStart;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $extramuralStart;

    public function __construct(){}
}
