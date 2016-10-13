<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Group;
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
}