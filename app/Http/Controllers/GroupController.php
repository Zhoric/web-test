<?php

namespace App\Http\Controllers;

use Group;
use Illuminate\Http\Request;

use Managers\GroupManager;
use User;

class GroupController extends Controller
{
    private $_groupManager;

    public function __construct(GroupManager $groupManager)
    {
        $this->_groupManager = $groupManager;
    }

    public function getAll()
    {
        return json_encode($this->_groupManager->getAll());
    }

    public function getGroup($id){
        return json_encode($this->_groupManager->getGroup($id));
    }

    public function create(Request $request){
        $groupData = $request->json('group');
        $studyPlanId = $request->json('studyPlanId');

        $group = new Group();
        $group->fillFromJson($groupData);
        $this->_groupManager->addGroup($group, $studyPlanId);
    }

    public function update(Request $request){
        $groupData = $request->json('group');
        $studyPlanId = $request->json('studyPlanId');

        $group = new Group();
        $group->fillFromJson($groupData);
        $this->_groupManager->updateGroup($group, $studyPlanId);
    }

    public function delete($groupId){
        $this->_groupManager->deleteGroup($groupId);
    }

    //    Работа со студентами группы
    public function getGroupStudents($groupId){
        return json_encode($this->_groupManager->getGroupStudents($groupId));
    }

    public function createStudent(Request $request){
        $studentData = $request->json('student');
        $groupId = $request->json('groupId');

        $student = new User();
        $student->fillFromJson($studentData);
        $this->_groupManager->addStudent($student, $groupId);
    }

    public function updateStudent(Request $request){
        $studentData = $request->json('student');

        $student = new User();
        $student->fillFromJson($studentData);
        $this->_groupManager->updateStudent($student);
    }

    public function setStudentGroup(Request $request){
        $studentId = $request->json('studentId');
        $groupId = $request->json('groupId');

        $this->_groupManager->setStudentGroup($studentId, $groupId);
    }

    public function deleteStudent($id){
        $this->_groupManager->deleteStudent($id);
    }
}
