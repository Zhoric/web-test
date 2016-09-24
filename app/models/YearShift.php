<?php

namespace App\Models;

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
     *  @ORM\Relation(type="belongsTo", relatedEntity="App\Models\User")
     */
    public  $id;

    /**
     * @ORM\Column(type="smallInteger")
     */
    public $yearsCount;
}
