<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Test;

class TestRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Test::class);
    }

    public function getByDiscipline($disciplineId){
        return $this->repo->findBy(['discipline' => $disciplineId]);
    }
}