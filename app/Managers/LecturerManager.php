<?php

namespace Managers;

use Discipline;
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
        return $this->_unitOfWork->users()
            ->getByNameAndRolePaginated($pageSize, $pageNum, UserRole::Lecturer, $name);
    }

    public function addLecturer(User $lecturer, $disciplinesIds){
        $this->_unitOfWork->users()->create($lecturer);
        $this->_unitOfWork->commit();

        $lecturerId = $lecturer->getId();
        $this->_unitOfWork->users()->setUserRole($lecturerId, UserRole::Lecturer);

        $this->_unitOfWork->disciplines()
            ->setLecturerDisciplines($lecturerId, $disciplinesIds);

        $this->_unitOfWork->commit();
    }

    public function updateLecturer(User $lecturer, $disciplinesIds){
        $this->_unitOfWork->users()->update($lecturer);
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