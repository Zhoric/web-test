<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use ResultSelectionCriterion;
use Test;
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
        $countQuery = $qb->where('tr.user = ' .$userId.'AND tr.test = '.$testId)
            ->select($qb->expr()->count('tr.id'))
            ->getQuery();

        return $countQuery->getSingleScalarResult();
    }

    public function getResults($testId, $groupId, $disciplineId)
    {
        $query = $this->repo->createQueryBuilder('tr');

        $query = $query->leftJoin(\TestResult::class,'tr2'
            , Join::WITH
            ,'tr.user= tr2.user AND tr.test = tr2.test AND tr2.attempt > tr.attempt')
            ->where('tr2.attempt is NULL')
            ->join(\StudentGroup::class,'sg',Join::WITH,'tr.user = sg.student')
            ->join(User::class, 'u', Join::WITH, 'tr.user = u.id AND sg.student = u.id')
            ->join(\Test::class,'t',Join::WITH,'tr.test = t.id');


        if($groupId != 0){
            $query =  $query->andWhere('sg.group = :groupId')
                ->setParameter('groupId', $groupId);
        }

        if($testId != 0){
            $query =  $query->andWhere('tr.test = :testId')
                ->setParameter('testId', $testId);
        }


        $query->andWhere('t.discipline = :disciplineId')
            ->setParameter('disciplineId',$disciplineId);


        return $query->orderBy('u.lastname')->getQuery()->execute();

    }

    public function getByUserAndDiscipline($userId, $disciplineId){

        $qb = $this->repo->createQueryBuilder('tr');
        $query = $qb
            ->join(Test::class, 't', Join::WITH, "t.discipline = $disciplineId 
            AND tr.test = t.id AND tr.user = $userId")
            ->leftJoin(TestResult::class, 'tr2', Join::WITH, "tr.test = tr2.test 
            AND tr.user = tr2.user AND tr2.attempt > tr.attempt")
            ->where('tr2.id is NULL')
            ->orderBy('tr.attempt', 'DESC')
            ->getQuery();

        return $query->execute();
    }

    public function getByUserAndDisciplineBetweenDates($userId, $disciplineId, $startDate, $endDate, $selectionCriterion){

        switch ($selectionCriterion){
            case ResultSelectionCriterion::LastAttempt:{
                $selectionExpression = 'tr2.attempt > tr.attempt';
                break;
            }
            case ResultSelectionCriterion::MaxMark:{
                $selectionExpression = 'tr2.mark > tr.mark';
                break;
            }
            case ResultSelectionCriterion::FirstAttempt:{
                $selectionExpression = 'tr2.attempt < tr.attempt';
                break;
            }
            default: throw new Exception('Указанный критерий выборки результатов не поддерживается!');
        }

        $qb = $this->repo->createQueryBuilder('tr');
        $query = $qb
            ->join(Test::class, 't', Join::WITH, "t.discipline = $disciplineId AND tr.user = $userId AND tr.test = t.id")
            ->leftJoin(TestResult::class, 'tr2', Join::WITH, "tr.test = tr2.test 
            AND tr.user = tr2.user AND tr.test = tr2.test AND $selectionExpression")
            ->where("tr2.id is NULL AND tr.dateTime >= '$startDate' AND tr.dateTime <= '$endDate'")
            ->getQuery();

        return $query->execute();
    }

    public function getLastForUser($userId, $testId){
        try{
            $qb = $this->repo->createQueryBuilder('tr');
            $query = $qb->where('tr.user = :user AND tr.test = :test')
                ->setParameter('user', $userId)
                ->setParameter('test', $testId)
                ->orderBy('tr.attempt', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

            return $query->getOneOrNullResult();
        } catch (Exception $exception){
            return null;
        }
    }

    public function getByUserAndTest($userId, $testId){
        return $this->repo->findBy(['user' => $userId, 'test' => $testId]);
    }

    public function deleteOlderThan($dateTime){
        $qb = $this->repo->createQueryBuilder('tr');
        $deleteQuery = $qb->delete()
            ->where('tr.dateTime < :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->getQuery();

        return $deleteQuery->execute();
    }
}