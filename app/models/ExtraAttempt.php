<?php

namespace App\models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="extra_attempts")
 */
class ExtraAttempt extends Entity
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\AutoIncrement
     */
    public $id;

    /**
     *  @ORM\Relation(type="belongsTo", relatedEntity="App\models\User")
     */
    public $userId;

    /**
     *  @ORM\Relation(type="belongsTo", relatedEntity="App\models\Test")
     */
    public $testId;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $count;
}
