<?php

namespace Managers;

use App\models\Group;
use App\models\Profile;
use Helpers\GroupHelper;
use Repositories\GroupRepository;
use Repositories\UserRepository;

class GroupManager
{
    private $_userRepo;
    private $_groupRepo;

    public function __construct(UserRepository $userRepo, GroupRepository $groupRepo)
    {
        $this->_userRepo = $userRepo;
        $this->_groupRepo = $groupRepo;
    }

    public function addGroup($profileId, $prefix, $studyYear, $isFullTime, $number)
    {
        $group = new Group();

        $profile = new Profile();
        $profile->id = $profileId;

        $group->profile = $profile;
        $group->prefix = $prefix;
        $group->studyYear = $studyYear;
        $group->isFullTime = $isFullTime;
        $group->number = $number;
        $group->name = GroupHelper::GenerateGroupName($prefix, $studyYear, $number, $isFullTime);

        return $this->_groupRepo->create($group);
    }

    public function updateGroup($groupId, $profileId, $prefix, $studyYear, $isFullTime, $number)
    {
        $groupToUpdate = $this->_groupRepo->find($groupId);

        $groupToUpdate->profileId = $profileId;
        $groupToUpdate->prefix = $prefix;
        $groupToUpdate->studyYear = $studyYear;
        $groupToUpdate->isFullTime = $isFullTime;
        $groupToUpdate->number = $number;
        $groupToUpdate->name = GroupHelper::GenerateGroupName($prefix, $studyYear, $number, $isFullTime);

        return $this->_groupRepo->update($groupToUpdate);
    }

    public function deleteGroup($groupId)
    {
        $group = $this->_groupRepo->findWith($groupId, 'students');

        if ($group != null){
            $this->_groupRepo->delete($group);

            $groupStudents = $group->students;

            foreach ($groupStudents as $student){
                $this->_userRepo->delete($student);
            }
        }
    }

    public function changeGroupName($groupId, $name)
    {
        $group = $this->_groupRepo->find($groupId);
        $group->name = $name;

        $this->_groupRepo->update($group);
    }

    public function getGroup($groupId)
    {
        return $this->_groupRepo->find($groupId);
    }

    public function searchGroupsByName($name)
    {
        $this->_groupRepo->where('name','like','%'.$name.'%');
    }

    public function getGroupWithStudents($groupId)
    {
        return $this->_groupRepo->whereWith('id','=',$groupId, 'students', true);
    }

    public function changeStudentsGroup($oldGroupId, $newGroupId)
    {
        $oldGroupStudents = $this->_groupRepo
            ->findWith($oldGroupId, 'students')
            ->students;

        foreach ($oldGroupStudents as $student){
            $student->groupId = $newGroupId;
            $this->_userRepo->update($student);
        }
    }
}