<?php

namespace App\Models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="themes")
 */
class Theme extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Relation(type="belongsToMany", relatedEntity="App\Models\Test", inverse=false)
     */
    public $testId;

    /**
     * @ORM\Relation(type="belongsTo", relatedEntity="App\Models\Discipline", inverse=false)
     */
    public $disciplineId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $name;
}
