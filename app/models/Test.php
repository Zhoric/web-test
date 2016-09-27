<?php

namespace App\models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="tests")
 */
class Test extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Relation(type="belongsTo", relatedEntity="App\models\Discipline")
     */
    public $discipline;

    /**
     * @ORM\Column(type="string", length=200)
     */
    public $subject;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $timeTotal;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $attempts;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $orderNumber;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $fullTimeStart;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $extramuralStart;

    public function __construct(){}
}
