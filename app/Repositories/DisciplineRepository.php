<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Discipline;

class DisciplineRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Discipline::class);
    }
}