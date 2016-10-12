<?php

namespace Managers;

use Repositories\UnitOfWork;

class GroupManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getAll(){
        return $this->_unitOfWork->groups()->all();
    }

    public function addGroup(\Group $group){
        $this->_unitOfWork->groups()->create($group);
        $this->_unitOfWork->commit();
    }

    public function updateGroup(\Group $group){
        $this->_unitOfWork->groups()->update($group);
        $this->_unitOfWork->commit();
    }

    public function setStudentGroup($groupId, $studentId){

    }

    public function deleteStudent($studentId){

    }

    public function addStudent(User $user){

    }


}