<?php

namespace App\Models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="input_params")
 */
class InputParam extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     * @ORM\Relation(type="belongsTo", relatedEntity="App\Models\ParamsSet")
     */
    public $id;

    /**
     * @ORM\Column(type="smallInteger", length=100)
     */
    public $number;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $type;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $value;
}
