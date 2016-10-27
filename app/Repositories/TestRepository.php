<?php

namespace Repositories;

use Discipline;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Illuminate\Support\Facades\DB;
use PaginationResult;
use Test;
use TestTheme;

class TestRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Test::class);
    }

    public function getByDiscipline($disciplineId){
        return $this->repo->findBy(['discipline' => $disciplineId]);
    }

    function setTestThemes($testId, array $themeIds){
        $qb = $this->em->getRepository(TestTheme::class)->createQueryBuilder('tt');
        $deleteQuery = $qb->delete()
            ->where('tt.test = :test')
            ->setParameter('test', $testId)
            ->getQuery();

        $deleteQuery->execute();

        foreach ($themeIds as $themeId){
            DB::table('test_theme')
                ->insert(  array(
                    'test_id' => $testId,
                    'theme_id' => $themeId
                ));
        }
    }

    public function getByNameAndDisciplinePaginated($pageSize, $pageNum, $disciplineId = null, $name = null){
        $qb = $this->repo->createQueryBuilder('t');
        $query = $qb;

        if ($disciplineId != null){
            $query = $query->join(Discipline::class, 'd', Join::WITH,
                't.discipline = d.id AND t.discipline = :disciplineId')
                ->setParameter('disciplineId', $disciplineId);
        }

        if ($name != null && $name != ''){
            $query = $query->where('t.subject LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        $countQuery = clone $query;
        $data =  $this->paginate($pageSize, $pageNum, $query, 't.subject');

        $count = $countQuery->select(
            $qb->expr()
                ->count('t.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginationResult($data, $count);
    }
}