<?php

namespace App\Models;

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
     * @ORM\Relation(type="belongsToMany", relatedEntity="App\Models\User", inverse=false, pivotTable="discipline_lecturer")
     */
    public $id;

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

}
