<?php

namespace App\Http\Controllers;

use Discipline;
use Illuminate\Http\Request;
use Managers\DisciplineManager;


class DisciplineController extends Controller
{
    private $_disciplineManager;

    public function __construct(DisciplineManager $disciplineManager)
    {
        $this->_disciplineManager = $disciplineManager;
    }

    public function getAll()
    {
        return json_encode($this->_groupManager->getAll());
    }

    public function getByNameAndProfilePaginated(Request $request){
        $pageNum =  $request->query('page');
        $pageSize = $request->query('pageSize');
        $profileId = $request->query('profile');
        $name = $request->query('name');

        $paginationResult = $this->_disciplineManager->getByNameAndProfilePaginated($pageNum, $pageSize, $name, $profileId);

        return json_encode($paginationResult);
    }

    public function create(Request $request){
        $disciplineData = $request->json('discipline');

        $group = new Discipline();
        $group->fillFromJson($disciplineData);
    }

    public function update(Request $request){
        $disciplineData = $request->json('discipline');

        $group = new Discipline();
        $group->fillFromJson($disciplineData);
    }

    public function delete($id){
        $this->_disciplineManager->deleteDiscipline($id);
    }
}
