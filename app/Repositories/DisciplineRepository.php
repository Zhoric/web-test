<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Discipline;
use Illuminate\Support\Facades\DB;
use PaginationResult;
use ProfileDiscipline;

class DisciplineRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Discipline::class);
    }

    public function getByNameAndProfilePaginated($pageSize, $pageNum, $profileId = null, $name = null){
        $qb = $this->repo->createQueryBuilder('d');
        $query = $qb;

        if ($profileId != null){
            $query = $query->join(ProfileDiscipline::class, 'pd', Join::WITH,
                'pd.discipline = d.id AND pd.profile = :profileId')
                ->setParameter('profileId', $profileId);
        }

        if ($name != null || $name != ''){
            $query = $query->where('d.name LIKE :name')
                ->orWhere('d.abbreviation LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        $countQuery = clone $query;
        $data =  $this->paginate($pageSize, $pageNum, $query, 'd.name');

        $count = $countQuery->select(
            $qb->expr()
                ->count('d.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginationResult($data, $count);
    }

    function setDisciplineProfiles($disciplineId, array $profileIds){

        $qb = $this->em->getRepository(ProfileDiscipline::class)->createQueryBuilder('pd');
        $deleteQuery = $qb->delete()
            ->where('pd.discipline = :discipline')
            ->setParameter('discipline', $disciplineId)
            ->getQuery();

        $deleteQuery->execute();

        foreach ($profileIds as $profileId){
            DB::table('profile_discipline')
                ->insert(  array(
                    'profile_id' => $profileId,
                    'discipline_id' => $disciplineId
                ));
        }
    }

}