<?php

namespace Repositories;

use DisciplineLecturer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Discipline;
use Group;
use Illuminate\Support\Facades\DB;
use PaginationResult;
use ProfileDiscipline;
use Test;
use TestResult;
use TestTheme;
use Theme;

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

        if ($name != null && $name != ''){
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

    function setLecturerDisciplines($lecturerId, array $disciplineIds){
        $qb = $this->em->getRepository(DisciplineLecturer::class)->createQueryBuilder('dl');
        $deleteQuery = $qb->delete()
            ->where('dl.lecturer = :lecturer')
            ->setParameter('lecturer', $lecturerId)
            ->getQuery();

        $deleteQuery->execute();

        foreach ($disciplineIds as $disciplineId){
            DB::table('discipline_lecturer')
                ->insert(  array(
                    'discipline_id' => $disciplineId,
                    'lecturer_id' => $lecturerId
                ));
        }
    }

    function getActualDisciplinesForGroup($groupId, $currentSemester){
        $qb = $this->repo->createQueryBuilder('d');
        $query = $qb->join(\DisciplinePlan::class, 'dp', Join::WITH, 'dp.discipline = d.id')
            ->join(Group::class, 'g', Join::WITH, 'g.studyplan = dp.studyplan')
            ->join(Theme::class, 'th', Join::WITH, 'th.discipline = d.id')
            ->join(TestTheme::class, 'tt', Join::WITH, 'tt.theme = th.id')
            ->join(Test::class, 't', Join::WITH, 'tt.test = t.id AND t.isActive = true')
            ->where('g.id = :groupId AND dp.startSemester <= :currentSemester')
            ->setParameter('groupId', $groupId)
            ->setParameter('currentSemester', $currentSemester)
            ->orderBy('dp.startSemester', 'DESC')

            ->getQuery();

        return $query->execute();
    }

    function getByProfile($profileId){
        $qb = $this->repo->createQueryBuilder('d');
        $query = $qb->join(ProfileDiscipline::class, 'pd', Join::WITH, 'pd.discipline = d.id')
            ->where('pd.profile = :profile')
            ->setParameter('profile', $profileId)
            ->getQuery();

        return $query->execute();
    }

    function getDisciplinesWhereTestsPassed($userId){
        $qb = $this->repo->createQueryBuilder('d');
        $query = $qb->join(Test::class, 't', Join::WITH, 't.discipline = d.id')
            ->join(TestResult::class, 'tr', Join::WITH, 'tr.test = t.id AND tr.user = :user')
            ->setParameter('user', $userId)
            ->distinct()
            ->getQuery();

        return $query->execute();
    }

    function getByLecturer($lecturerId){
        $qb = $this->repo->createQueryBuilder('d');
        $query = $qb
            ->join(DisciplineLecturer::class, 'dl', Join::WITH, 'dl.discipline = d.id AND dl.lecturer = :lecturer')
            ->setParameter('lecturer', $lecturerId)
            ->getQuery();

        return $query->execute();
    }

}