<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use PaginationResult;
use TestResult;
use User;

class TestResultRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, TestResult::class);
    }

    public function getLastAttemptNumber($testId, $userId)
    {
        $qb = $this->repo->createQueryBuilder('tr');
        $countQuery = $qb->where('tr.user = ' . $testId)
            ->where('tr.test = ' . $userId)
            ->select($qb->expr()->count('tr.id'))
            ->getQuery();

        return $countQuery->getSingleScalarResult();
    }

    public function getByGroupAndTest($testId, $groupId)
    {
        $sql = "SELECT * FROM test_result tr
                JOIN student_group sg
                ON sg.group_id = ?
                JOIN user u 
                ON tr.user_id = u.id AND sg.student_id = u.id
                AND tr.date_time = 
                (SELECT MAX(date_time) from test_result tr2
                    where tr2.test_id = ? AND tr2.user_id = u.id)";

        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata(TestResult::class, 'tr');
        $rsm->addJoinedEntityFromClassMetadata(User::class, 'u', 'tr', 'user', array('id' => 'user_id'));

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $groupId);
        $query->setParameter(2, $testId);

        return $query->getArrayResult();
    }
}