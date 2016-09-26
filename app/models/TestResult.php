<?php

namespace App\models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_results")
 */
class TestResult extends Entity
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\AutoIncrement
     */
    public  $id;


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
    public $mark;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $attempt;

    /**
     * @ORM\Column(type="dateTime", nullable=true)
     */
    public $dateTime;
}
