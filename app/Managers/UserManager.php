<?php

namespace Managers;

use Mockery\CountValidator\Exception;
use Repositories\UnitOfWork;

class UserManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function addUser(User $user)
    {
        //TODO: Проверять уникальность email'a
        $this->_unitOfWork->users()->create($user);
        $this->_unitOfWork->commit();
    }

    public function getUser($id)
    {
        return $this->_unitOfWork->users()->find($id);
    }

    public function getUserByRememberToken($id,$token){
     //   return $this->_userRepo->findByRememberToken($id,$token);
    }

    public function getUserRole($id){
        $role = $this->_unitOfWork->userRoles()->getRoleByUser($id);
        if(isset($role)) {
            return $role->getSlug();
        }
        else throw new Exception('Невозможно получить роль текущего пользователя.');
    }



}