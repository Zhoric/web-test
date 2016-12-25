<?php

namespace App\Http\Controllers;

use Discipline;
use Exception;
use Illuminate\Http\Request;
use Managers\LecturerManager;
use User;


class LecturerController extends Controller
{
    private $_lecturerManager;

    public function __construct(LecturerManager $lecturerManager)
    {
        $this->_lecturerManager = $lecturerManager;
    }

    public function getByNamePaginated(Request $request){
        try{
            $pageNum =  $request->query('page');
            $pageSize = $request->query('pageSize');
            $name = $request->query('name');

            $lecturers = $this->_lecturerManager->getByNamePaginated($pageNum, $pageSize, $name);

            return $this->successJSONResponse($lecturers);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function create(Request $request){
        try{
            $lecturerData = $request->json('lecturer');
            $disciplineIds = $request->json('disciplineIds');

            $lecturer = new User();
            $lecturer->fillFromJson($lecturerData);
            $this->_lecturerManager->addLecturer($lecturer, $disciplineIds);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function update(Request $request){
        try{
            $lecturerData = $request->json('lecturer');
            $disciplineIds = $request->json('disciplineIds');

            $lecturer = new User();
            $lecturer->fillFromJson($lecturerData);
            $this->_lecturerManager->updateLecturer($lecturer, $disciplineIds);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($id){
        return $this->_lecturerManager->deleteLecturer($id);
    }
}
