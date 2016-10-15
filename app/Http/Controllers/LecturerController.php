<?php

namespace App\Http\Controllers;

use Discipline;
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

    //TODO: получать преподавателя со всеми id дисциплин
    public function getByNamePaginated(Request $request){
        $pageNum =  $request->query('page');
        $pageSize = $request->query('pageSize');
        $name = $request->query('name');

        $paginationResult = $this->_lecturerManager
            ->getByNamePaginated($pageNum, $pageSize, $name);

        return json_encode($paginationResult);
    }

    public function create(Request $request){
        $lecturerData = $request->json('lecturer');
        $disciplineIds = $request->json('disciplineIds');

        $lecturer = new User();
        $lecturer->fillFromJson($lecturerData);
        $this->_lecturerManager->addLecturer($lecturer, $disciplineIds);
    }

    public function update(Request $request){
        $lecturerData = $request->json('lecturer');
        $disciplineIds = $request->json('disciplineIds');

        $lecturer = new User();
        $lecturer->fillFromJson($lecturerData);
        $this->_lecturerManager->updateLecturer($lecturer, $disciplineIds);
    }

    public function delete($id){
        $this->_disciplineManager->deleteDiscipline($id);
    }
}
