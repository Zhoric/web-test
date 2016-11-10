<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\Str;
use PaginationResult;
use Repositories\Interfaces\IUserRepository;
use Doctrine\ORM\Query\Expr\Join;
use RoleUser;
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

    public function setUserRole($userId, $roleId){
        DB::table('role_user')
            ->insert(  array(
                'user_id' => $userId,
                'role_id' => $roleId
            ));
    }

    public function getByNameAndRolePaginated($pageSize, $pageNum, $roleId, $name = null){
        $qb = $this->repo->createQueryBuilder('u');
        $query = $qb;

        $query = $query->join(RoleUser::class, 'ru', Join::WITH,
            'ru.user = u.id AND ru.role = :roleId')
            ->setParameter('roleId', $roleId);


        if ($name != null && $name != ''){
            $query = $query->where('u.firstname LIKE :name')
                ->orWhere('u.lastname LIKE :name')
                ->orWhere('u.patronymic LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        $countQuery = clone $query;
        $data =  $this->paginate($pageSize, $pageNum, $query, 'u.lastname');

        $count = $countQuery->select(
            $qb->expr()
                ->count('u.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginationResult($data, $count);
    }
}