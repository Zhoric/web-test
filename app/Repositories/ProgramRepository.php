<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;

use Program;


class ProgramRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Program::class);
    }

    public function getByQuestion($questionId){
        return $this->repo->findOneBy(['question' => $questionId]);
    }
}