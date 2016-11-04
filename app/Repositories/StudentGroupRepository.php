<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use StudentGroup;

class StudentGroupRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, StudentGroup::class);
    }
    
    public function getUserGroup($userId){
        return $this->repo->findOneBy(['student' => $userId]);
    }
}
