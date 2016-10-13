<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\Str;
use Repositories\Interfaces\IUserRepository;
use Doctrine\ORM\Query\Expr\Join;
use User;

class UserRepository extends BaseRepository implements IUserRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, User::class);
    }

    public function findByRememberToken($id,$token){
        return $this->where('remember_token','=',$token)->where('id','=',$id)->first();
    }

    public function findByCredentials(array $credentials){
        if (empty($credentials)) {
            return;
        }

        $query = $this;
        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, 'password')) {
                $query = $this->where($key,'=', $value);
            }
        }

        return $query->first();
    }

    public function findByEmail($login)
    {
        return $this->where('email','=',$login)->first();
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
}