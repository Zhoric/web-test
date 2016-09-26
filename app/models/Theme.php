<?php

namespace App\models;

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
     * @ORM\Relation(type="belongsToMany", relatedEntity="App\models\Test", inverse=false)
     */
    public $testId;

    /**
     * @ORM\Relation(type="hasMany", relatedEntity="App\models\Question", inverse=true)
     */
    public $questions;

    /**
     * @ORM\Relation(type="belongsTo", relatedEntity="App\models\Discipline", inverse=false)
     */
    public $disciplineId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $name;
}
