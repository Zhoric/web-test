<?php

namespace App\Models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="programs")
 */
class Program extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     * @ORM\Relation(type="belongsTo", relatedEntity="App\Models\Question")
     */
    public $id;

    /**
     * @ORM\Column(type="text")
     */
    public $template;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $lang;
}
