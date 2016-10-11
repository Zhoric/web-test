<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Group;

class GroupRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Group::class);
    }

    public function getGroupsByProfile($profileId){
        $query = $this->repo->createQueryBuilder('g')
            ->join(\Studyplan::class, 'sp', Join::WITH,
                'g.studyplan = sp.id AND sp.profile = '.$profileId);
        return $query->getQuery()->execute();
    }
}