<?php

namespace App\Models;

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
     * @ORM\Relation(type="belongsTo", relatedEntity="App\Models\Program")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $expectedOutput;
}
