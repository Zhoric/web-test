<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use DisciplinePlan;
use Discipline;
use PaginationResult;


class DisciplinePlanRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, DisciplinePlan::class);
    }

    public function getPlansDisciplinesByStudyplanAndNamePaginated($pageSize, $pageNum, $studyplanId, $name = null){
        $qb = $this->repo->createQueryBuilder('dp');
        $query = $qb;

        $query = $query->join(Discipline::class, 'd', Join::WITH, 'd.id = dp.discipline')
            ->select('d.name AS discipline, d.id AS disciplineId, dp.id, dp.semester, 
            dp.hoursAll, dp.hoursLecture, dp.hoursPractical, dp.hoursLaboratory, dp.hoursSolo, 
            dp.countLecture, dp.countPractical, dp.countLaboratory, 
            dp.hasExam, dp.hasCoursework, dp.hasCourseProject, dp.hasDesignAssignment, dp.hasEssay, dp.hasAudienceTest, dp.hasHomeTest')
            ->where('dp.studyplan = :studyplan');

        $query->setParameter('studyplan',$studyplanId);

        if ($name != null && $name != ''){
            $query = $query->andWhere('d.name LIKE :name');
            $query->setParameter('name', "%{$name}%");
        }

        $countQuery = clone $query;
        $data =  $this->paginate($pageSize, $pageNum, $query, 'dp.semester');

        $count = $countQuery->select(
            $qb->expr()
            ->count('dp.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginationResult($data, $count);
    }
}