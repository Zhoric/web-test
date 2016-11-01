<?php

namespace Repositories;

use ExtraAttempt;
use Doctrine\ORM\EntityManager;

class ExtraAttemptRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, ExtraAttempt::class);
    }

    public function findByTestAndUser($testId, $userId){
        return $this->repo->findOneBy(['test' => $testId, 'user' => $userId]);
    }
}