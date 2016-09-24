<?php

namespace App\Models;

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
     * @ORM\Relation(type="belongsTo", relatedEntity="App\Models\Discipline")
     */
    public $id;

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
}
