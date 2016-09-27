<?php

namespace App\models;

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
     */
    public  $id;

    /**
     *  @ORM\Relation(type="belongsTo", relatedEntity="App\models\Question")
     */
    public $question;

    /**
     * @ORM\Column(type="text")
     */
    public $text;

    /**
     * @ORM\Column(type="boolean")
     */
    public $isRight;

    public function __construct(){}
}
