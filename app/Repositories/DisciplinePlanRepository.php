<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use DisciplinePlan;

class DisciplinePlanRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, DisciplinePlan::class);
    }
}