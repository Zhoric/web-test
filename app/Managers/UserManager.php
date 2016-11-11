<?php

namespace Managers;

use Illuminate\Support\Facades\Auth;
use Mockery\CountValidator\Exception;
use Repositories\UnitOfWork;
use UserInfoViewModel;

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
            return $role;
        }
        else throw new Exception('Невозможно получить роль текущего пользователя.');
    }

    public function getCurrentUserInfo(){
        $currentUser = Auth::user();
        if (!isset($currentUser)){
            throw new Exception('Невозможно получить данные пользователя. Необходима авторизация!');
        }
        $currentUserRole = $this->getUserRole($currentUser->getId());

        $userInfo = new UserInfoViewModel();
        $userInfo->fillFromUser($currentUser);
        $userInfo->setRole($currentUserRole->getSlug());

        return $userInfo;
    }

}