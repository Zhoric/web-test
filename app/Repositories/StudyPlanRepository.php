<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Studyplan;

class StudyPlanRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Studyplan::class);
    }
}