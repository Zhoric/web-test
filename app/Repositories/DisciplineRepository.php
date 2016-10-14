<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Discipline;
use ProfileDiscipline;

class DisciplineRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Discipline::class);
    }

    public function getByNameAndProfilePaginated($disciplineName, $pageSize,
                                                 $pageNumber, $profileId = null)
    {
        $qb = $this->repo->createQueryBuilder('d');
        $query = $qb->where('Discipline.name LIKE :disciplineName')
            ->setParameter('disciplineName', '%'.$disciplineName.'%');

        if ($profileId != null){
            $query = $query->join(ProfileDiscipline::class, 'pd', Join::WITH,
                'pd.discipline = d.id AND pd.profile = :profileId')
                ->setParameter('profileId', $profileId);
        }

        return $this->paginate($pageSize, $pageNumber, $query, 'name');
    }

}