<?php

namespace Managers;

use Illuminate\Support\Facades\Auth;
use Mockery\CountValidator\Exception;
use Repositories\UnitOfWork;
use Role;
use User;
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

    public function activate($userId){

        /** @var User $user */
        $user = $this->_unitOfWork->users()->find($userId);
        if (!isset($user)){
            throw new Exception('Пользователь не найден!');
        }
        $user->setActive(true);
        $this->_unitOfWork->users()->update($user);
        $this->_unitOfWork->commit();
    }

    public function getUserByRememberToken($id, $token)
    {
        //   return $this->_userRepo->findByRememberToken($id,$token);
    }

    /**
     * @param $id
     * @return Role
     */
    public function getUserRole($id)
    {
        $role = $this->_unitOfWork->userRoles()->getRoleByUser($id);
        if (isset($role)) {
            return $role;
        } else throw new Exception('Невозможно получить роль текущего пользователя.');
    }

    public function update($user)
    {
        $this->_unitOfWork->users()->update($user);
        $this->_unitOfWork->commit();
    }

    public function getCurrentUserInfo()
    {
        $currentUser = Auth::user();
        if (!isset($currentUser)) {
            throw new Exception('Невозможно получить данные пользователя. Необходима авторизация!');
        }
        $currentUserRole = $this->getUserRole($currentUser->getId());

        $userInfo = new UserInfoViewModel();
        $userInfo->fillFromUser($currentUser);
        $userInfo->setRole($currentUserRole->getSlug());

        return $userInfo;
    }

    public function getStudentInfo($userId)
    {
        $user = $this->_unitOfWork->users()->find($userId);
        if (!isset($user)){
            throw new Exception('Ошибка! Не удалось получить данные указанного пользователя.');
        }

        $userGroup = $this->_unitOfWork->studentGroups()->getUserGroup($userId);
        if (!isset($userGroup)){
            throw new Exception('Ошибка! Не удалось получить данные о группе студента.');
        }

        $userInfo = new UserInfoViewModel();
        $userInfo->fillFromUser($user);
        $userInfo->setGroup($userGroup->getGroup());

        return $userInfo;
    }

    public function deleteUser($userId)
    {
        $user = $this->_unitOfWork->users()->find($userId);
        if (!isset($user)) {
            throw new Exception('Указанный пользователь не найден!');
        }

        $this->_unitOfWork->users()->delete($user);
        $this->_unitOfWork->commit();
    }

    public function getByNameAndGroupPaginated($pageSize, $pageNum, $name, $groupName, $isActive)
    {
        return $this->_unitOfWork
            ->users()
            ->getByNameAndGroupPaginated($pageSize, $pageNum, $name, $groupName, $isActive);
    }

}