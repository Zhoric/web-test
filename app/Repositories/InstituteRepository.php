<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Institute;

class InstituteRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Institute::class);
    }
}