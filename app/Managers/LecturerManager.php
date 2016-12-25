<?php

namespace Managers;

use Discipline;
use Exception;
use LecturerInfoViewModel;
use PaginationResult;
use User;
use Repositories\UnitOfWork;
use UserRole;

class LecturerManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getByNamePaginated($pageNum, $pageSize, $name){
        $lecturersVms = [];
        $lecturers =  $this->_unitOfWork->users()
            ->getLecturersByNamePaginated($pageSize, $pageNum, $name);

        $lecturersData = $lecturers->getData();

        foreach ($lecturersData as $lecturer){
            $discplines = $this->_unitOfWork->disciplines()->getByLecturer($lecturer['id']);
            $lecturerVm = new LecturerInfoViewModel($lecturer, $discplines);
            array_push($lecturersVms, $lecturerVm);
        }

        return new PaginationResult($lecturersVms,$lecturers->getCount());
    }

    public function addLecturer(User $lecturer, $disciplinesIds){
        $lecturer->setPassword(bcrypt($lecturer->getPassword()));

        $this->_unitOfWork->users()->create($lecturer);
        $this->_unitOfWork->commit();

        $lecturerId = $lecturer->getId();
        $role = $this->_unitOfWork->roles()->getBySlug(UserRole::Lecturer);
        if (!isset($role)){
            throw new Exception('Невозможно найти роль пользователя '.UserRole::Lecturer);
        }
        $this->_unitOfWork->users()->setUserRole($lecturerId, $role->getId());

        $this->_unitOfWork->disciplines()
            ->setLecturerDisciplines($lecturerId, $disciplinesIds);

        $this->_unitOfWork->commit();
    }

    public function updateLecturer(User $lecturer, $disciplinesIds){

        /** @var User $oldUser */
        $oldUser = $this->_unitOfWork->users()->find($lecturer->getId());
        if (!isset($oldUser)){
            throw new Exception('Невозможно обновить данные преподавателя! Учётная запись не найдена!');
        }
        $oldUser->setActive($lecturer->getActive());
        $oldUser->setEmail($lecturer->getEmail());
        $oldUser->setFirstname($lecturer->getFirstname());
        $oldUser->setPatronymic($lecturer->getPatronymic());
        $oldUser->setLastname($lecturer->getLastname());

        $this->_unitOfWork->users()->update($oldUser);
        $this->_unitOfWork->commit();

        $lecturerId = $lecturer->getId();

        $this->_unitOfWork->disciplines()
            ->setLecturerDisciplines($lecturerId, $disciplinesIds);

        $this->_unitOfWork->commit();
    }

    public function deleteLecturer($id){
        $lecturer = $this->_unitOfWork->users()->find($id);
        if ($lecturer != null){
            $this->_unitOfWork->users()->delete($lecturer);
            $this->_unitOfWork->commit();
        }
    }



}