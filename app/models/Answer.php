<?php

namespace App\Models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="answers")
 */
class Answer extends Entity
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\AutoIncrement
     *  @ORM\Relation(type="belongsTo", relatedEntity="App\Models\Question")
     */
    public  $id;

    /**
     * @ORM\Column(type="text")
     */
    public $text;

    /**
     * @ORM\Column(type="boolean")
     */
    public $isRight;
}
