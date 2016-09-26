<?php

namespace App\models;

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
     */
    public $id;

    /**
     * @ORM\Relation(type="belongsTo", relatedEntity="App\models\ParamsSet")
     */
    public $paramsSet;

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
