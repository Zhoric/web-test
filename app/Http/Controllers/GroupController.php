<?php

namespace App\Http\Controllers;

use Exception;
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
        try{
            $groups = $this->_groupManager->getAll();
            return $this->successJSONResponse($groups);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getProfileGroupsByNamePaginated(Request $request){
        try{
            $pageNum =  $request->query('page');
            $pageSize = $request->query('pageSize');
            $profileId = $request->query('profile');
            $groupName = $request->query('name');

            $paginationResult = $this->_groupManager
                ->getProfileGroupsByNamePaginated($pageNum, $pageSize, $groupName, $profileId);

            return $this->successJSONResponse($paginationResult);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getGroup($id){
        try{
            $group = $this->_groupManager->getGroup($id);
            return $this->successJSONResponse($group);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function create(Request $request){
        try{
            $groupData = $request->json('group');
            $studyPlanId = $request->json('studyPlanId');

            $group = new Group();
            $group->fillFromJson($groupData);
            $this->_groupManager->addGroup($group, $studyPlanId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }


    public function update(Request $request){
        try{
            $groupData = $request->json('group');
            $studyPlanId = $request->json('studyPlanId');

            $group = new Group();
            $group->fillFromJson($groupData);
            $this->_groupManager->updateGroup($group, $studyPlanId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($groupId){
        try{
            $this->_groupManager->deleteGroup($groupId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /* ------------------------ Работа со студентами группы ---------------------------- */

    public function getGroupStudents($groupId){
        try{
            $students = $this->_groupManager->getGroupStudents($groupId);
            return $this->successJSONResponse($students);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function createStudent(Request $request){
        try{
            $studentData = $request->json('student');
            $groupId = $request->json('groupId');

            $student = new User();
            $student->fillFromJson($studentData);
            $this->_groupManager->addStudent($student, $groupId);

            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function updateStudent(Request $request){
        try{
            $studentData = $request->json('student');
            $groupId = $request->json('groupId');

            $student = new User();
            $student->fillFromJson($studentData);
            $this->_groupManager->updateStudent($student, $groupId);

            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function setStudentGroup(Request $request){
        try {
            $studentId = $request->json('studentId');
            $groupId = $request->json('groupId');

            $this->_groupManager->setStudentGroup($studentId, $groupId);
            return $this->successJSONResponse();
        } catch (Exception $exception) {
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function deleteStudent($id){
        try{
            $this->_groupManager->deleteStudent($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}
