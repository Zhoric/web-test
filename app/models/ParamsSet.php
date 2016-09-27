<?php

namespace App\models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="params_sets")
 */
class ParamsSet extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Relation(type="belongsTo", relatedEntity="App\models\Program")
     */
    public $program;

    /**
     * @ORM\Relation(type="hasMany", relatedEntity="App\models\ParamsSet", inverse=true)
     */
    public $inputParams;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $expectedOutput;

    public function __construct(){}
}
