<?php

namespace App\models;

use ProAI\Datamapper\Annotations as ORM;
use ProAI\Datamapper\Support\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="year_shifts")
 */
class YearShift extends Entity
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\AutoIncrement
     *  @ORM\Relation(type="belongsTo", relatedEntity="App\models\User", inverse=true)
     */
    public  $id;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $yearsCount;
}
