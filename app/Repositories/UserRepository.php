<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PaginationResult;
use Repositories\Interfaces\IUserRepository;
use Doctrine\ORM\Query\Expr\Join;
use Role;
use RoleUser;
use StudentGroup;
use User;
use UserRole;

class UserRepository extends BaseRepository implements IUserRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, User::class);
    }

    public function findByRememberToken($id,$token){
        return $this->repo->findBy(['rememberToken' => $token , 'id' => $id]);
    }

    public function findByCredentials(array $credentials){
        if (empty($credentials)) {
            return array();
        }



        return  $this->repo->findOneBy(['email' => $credentials['email']]);
    }

    public function findByEmail($email)
    {
        return $this->repo->findOneBy(['email' => $email]);
    }

    public function getGroupsByProfile($profileId){
        $query = $this->repo->createQueryBuilder('g')
            ->join(\Studyplan::class, 'sp', Join::WITH,
                'g.studyplan = sp.id AND sp.profile = '.$profileId);
        return $query->getQuery()->execute();
    }

    public function getGroupStudents($groupId){
        $query = $this->repo->createQueryBuilder('u')
            ->join(\StudentGroup::class, 'sg', Join::WITH,
            'u.id = sg.student AND sg.group = '.$groupId);
        return $query->getQuery()->execute();
    }

    public function getGroupStudentsIds($groupId){
        $query = $this->repo->createQueryBuilder('u')
            ->join(\StudentGroup::class, 'sg', Join::WITH,
                'u.id = sg.student AND sg.group = '.$groupId)
            ->select('u.id');
        return $query->getQuery()->execute();
    }

    public function getGroupUnactiveStudentsIds($groupId){
        $query = $this->repo->createQueryBuilder('u')
            ->join(\StudentGroup::class, 'sg', Join::WITH,
                "u.id = sg.student AND sg.group = $groupId AND u.active = false")
            ->select('u.id');
        return $query->getQuery()->execute();
    }

    public function setUserRole($userId, $roleId){
        DB::table('role_user')
            ->insert(  array(
                'user_id' => $userId,
                'role_id' => $roleId
            ));
    }

    public function getByNameAndGroupPaginated($pageSize, $pageNum, $name, $groupId, $isActive){
        $qb = $this->repo->createQueryBuilder('u');
        $query = $qb;

        $query = $query ->join(RoleUser::class, 'ru', Join::WITH, "ru.user = u.id")
            ->join(Role::class, 'r', Join::WITH, "ru.role = r.id AND r.slug LIKE '".UserRole::Student."'");

        if (isset($groupId) && !empty($groupId)){
            $query = $query
                ->join(StudentGroup::class, 'sg', Join::WITH, 'sg.student = u.id')
                ->join(Group::class, 'g', Join::WITH, "g.id = sg.group AND g.id = $groupId");
        }

        if (isset($name) && !empty($name)){
            $query = $query->andWhere("u.firstname LIKE '%".$name."%'")
                ->orWhere("u.lastname LIKE '%".$name."%'")
                ->orWhere("u.patronymic LIKE '%".$name."%'");
        }

        if (isset($isActive) && !empty($isActive)){
            $query = $query->andWhere("u.active = ".$isActive);
        }

        $countQuery = clone $query;
        $data =  $this->paginate($pageSize, $pageNum, $query, 'u.lastname');

        $count = $countQuery->select(
            $countQuery->expr()
                ->count('u.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginationResult($data, $count);
    }

    public function getGroupStudentsCount($groupId){
        $qb = $this->repo->createQueryBuilder('u');

        $query = $qb
            ->join(StudentGroup::class, 'sg', Join::WITH, 'sg.student = u.id')
            ->join(Group::class, 'g', Join::WITH, "g.id = sg.group AND g.id = $groupId");

        $count = $query->select(
            $query->expr()
                ->count('u.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }

    public function getLecturersByNamePaginated($pageSize, $pageNum, $name){
        $qb = $this->repo->createQueryBuilder('u');
        $query = $qb->join(RoleUser::class, 'ru', Join::WITH, 'ru.user = u.id')
            ->join(Role::class, 'r', Join::WITH, 'ru.role = r.id AND r.slug = :lecturer')
            ->setParameter(':lecturer', UserRole::Lecturer);

        if (isset($name)){
            $query = $query->where("u.lastname LIKE '%".$name."%'")
                ->orWhere("u.firstname LIKE '%".$name."%'");
        }

        $countQuery = clone $query;
        $data =  $this->paginate($pageSize, $pageNum, $query, 'u.lastname');

        $count = $countQuery->select(
            $qb->expr()
                ->count('u.lastname'))
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginationResult($data, $count);
    }



}