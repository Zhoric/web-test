<?php

namespace App\Models;

use ProAI\Datamapper\Support\Entity;
use ProAI\Datamapper\Annotations as ORM;

class BaseEntity extends Entity
{
    /**
     * @ORM\Id
     * @ORM\AutoIncrement
     * @ORM\Column(type="integer")
     */
    protected $id;
}