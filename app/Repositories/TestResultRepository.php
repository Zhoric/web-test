<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use TestResult;

class TestResultRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, TestResult::class);
    }

    public function getLastAttemptNumber($testId, $userId){
        $qb = $this->repo->createQueryBuilder('tr');
        $countQuery = $qb->where('tr.user = '.$testId)
            ->where('tr.test = '.$userId)
            ->select($qb->expr()->count('tr.id'))
            ->getQuery();

        return $countQuery->getSingleScalarResult();
    }
}