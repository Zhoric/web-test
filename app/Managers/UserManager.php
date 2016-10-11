<?php

namespace Managers;

use App\models\User;
use App\models\UserRole;
use Repositories\UnitOfWork;
use Repositories\UserRepository;

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
        $this->_unitOfWork->getUsersRepo()->create($user);
        $this->_unitOfWork->commit();

    }

    public function updateUser($id, $fullName, $year, $groupId)
    {
        $userToUpdate = $this->_userRepo->find($id);

        $userToUpdate->fullName = $fullName;
        $userToUpdate->admissionYear = $year;
        $userToUpdate->groupId = $groupId;

        $this->_userRepo->update($userToUpdate);
    }

    public function setStudentYearShift($userId, $yearsCount)
    {
        $user = $this->_userRepo->find($userId);
        $user->yearShift = $yearsCount;
        $this->_userRepo->update($user);
    }

    public function getUser($id)
    {
        return $this->_userRepo->find($id);
    }

    public function getUserByRememberToken($id,$token){
        return $this->_userRepo->findByRememberToken($id,$token);
    }

    public function deleteUser($id){

        $userToDelete = $this->_userRepo->find($id);

        if($userToDelete != null){
            $this->_userRepo->delete($userToDelete);
        }
    }

    public function getStudents()
    {
        return $this->_userRepo->where('role', '=', UserRole::Student);
    }

    public function getLecturers()
    {
        return $this->_userRepo->where('role', '=', UserRole::Lecturer);
    }
}