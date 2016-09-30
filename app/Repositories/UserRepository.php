<?php

namespace Repositories;

use Illuminate\Support\Str;
use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'User');
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

    public function findByLogin($login)
    {
        return $this->where('login','=',$login)->first();
    }
}