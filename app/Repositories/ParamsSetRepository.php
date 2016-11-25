<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;

use ParamsSet;


class ParamsSetRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, ParamsSet::class);
    }

    public function getByProgram($programId){
        return $this->repo->findBy(['program' => $programId]);
    }



}