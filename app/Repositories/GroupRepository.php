<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Group;
use PaginationResult;
use StudentGroup;

class GroupRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Group::class);
    }

    public function getGroupsByProfile($profileId){
        $query = $this->repo->createQueryBuilder('g')
            ->join(\Studyplan::class, 'sp', Join::WITH,
                'g.studyplan = sp.id AND sp.profile = '.$profileId)
            ->getQuery();

        return $query->execute();
    }

    public function setStudentsGroup($studentId, $groupId){
        $qb = $this->em->createQueryBuilder();
        $query = $qb->update(StudentGroup::class, 'sg')
            ->set('sg.group', $groupId)
            ->where('sg.student = '.$studentId)
            ->getQuery();

        $query->execute();
    }

    public function getByNameAndProfilePaginated($pageSize, $pageNum, $profileId = null, $name = null){
        $qb = $this->repo->createQueryBuilder('g');
        $query = $qb;

        if ($profileId != null){
            $query = $query->join(\Studyplan::class, 'sp', Join::WITH,
                'g.studyplan = sp.id AND sp.profile = :profileId')
                ->setParameter('profileId', $profileId);
        }

        if ($name != null || $name != ''){
            $query = $query->where('g.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        $countQuery = clone $query;
        $data =  $this->paginate($pageSize, $pageNum, $query, 'g.name');

        $count = $countQuery->select(
            $qb->expr()
                ->count('g.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginationResult($data, $count);
    }

















}