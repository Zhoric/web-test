<?php

namespace Managers;

use Group;
use Repositories\UnitOfWork;
use User;

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

    public function getGroup($id){
        return $this->_unitOfWork->groups()->find($id);
    }

    public function addGroup(Group $group, $studyPlanId){
        $studyplan = $this->_unitOfWork->studyPlans()->find($studyPlanId);
        $group->setStudyplan($studyplan);

        $this->_unitOfWork->groups()->create($group);
        $this->_unitOfWork->commit();
    }

    public function updateGroup(Group $group, $studyPlanId){
        $studyplan = $this->_unitOfWork->studyPlans()->find($studyPlanId);
        $group->setStudyplan($studyplan);

        $this->_unitOfWork->groups()->update($group);
        $this->_unitOfWork->commit();
    }

    public function deleteGroup($groupId){
        $group = $this->_unitOfWork->groups()->find($groupId);
        if ($group != null){
            $this->_unitOfWork->groups()->delete($group);
            $this->_unitOfWork->commit();
        }
    }

    // Работа со студентами группы
    public function getGroupStudents($groupId){
        return $this->_unitOfWork->users()->getGroupStudents($groupId);
    }

    public function setStudentGroup($groupId, $studentId){
        $this->_unitOfWork->groups()->
            setStudentsGroup($groupId, $studentId);
        $this->_unitOfWork->commit();
    }

    public function addStudent(User $student, $groupId){
        $this->_unitOfWork->users()->create($student);
        $this->_unitOfWork->commit();

        $this->_unitOfWork->groups()
            ->setStudentsGroup($student->getId(), $groupId);
        $this->_unitOfWork->commit();
    }

    public function updateStudent(User $student){
        $this->_unitOfWork->users()->update($student);
        $this->_unitOfWork->commit();
    }

    public function deleteStudent($studentId){
        $student = $this->_unitOfWork->users()->find($studentId);

        if ($student != null){
            $this->_unitOfWork->users()->delete($student);
            $this->_unitOfWork->commit();
        }
    }

}