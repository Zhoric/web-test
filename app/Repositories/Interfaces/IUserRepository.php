<?php

namespace Repositories\Interfaces;

interface IUserRepository extends IRepository
{

    public function findByRememberToken($id,$token);
    public function findByCredentials(array $credentials);
    public function findByEmail($login);

}