<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Theme;

class ThemeRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Theme::class);
    }

    public function getByDiscipline($disciplineId){
        return $this->repo->findBy(['discipline' => $disciplineId]);
    }

    public function getByTest($testId){
        $qb = $this->repo->createQueryBuilder('t');
        $query = $qb->join(\TestTheme::class, 'tt', Join::WITH, 'tt.test = t.id')
            ->where('t.id = :testId')
            ->setParameter('testId', $testId)
            ->getQuery();

        return $query->execute();
    }
}