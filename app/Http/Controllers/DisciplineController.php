<?php

namespace App\Http\Controllers;

use Discipline;
use Illuminate\Http\Request;
use Managers\DisciplineManager;
use Theme;


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
        $profileIds = $request->json('profileIds');

        $discipline = new Discipline();
        $discipline->fillFromJson($disciplineData);
        $this->_disciplineManager->addDiscipline($discipline, $profileIds);
    }

    public function update(Request $request){
        $disciplineData = $request->json('discipline');
        $profileIds = $request->json('profileIds');

        $discipline = new Discipline();
        $discipline->fillFromJson($disciplineData);
        $this->_disciplineManager->updateDiscipline($discipline, $profileIds);
    }

    public function delete($id){
        $this->_disciplineManager->deleteDiscipline($id);
    }

    public function getThemes($id){
        return json_encode($this->_disciplineManager->getDisciplineThemes($id));
    }

    public function addTheme(Request $request){
        $themeData = $request->json('theme');
        $disciplineId = $request->json('disciplineId');

        $theme = new Theme();
        $theme->fillFromJson($themeData);
        $this->_disciplineManager->addTheme($theme, $disciplineId);
    }

    public function updateTheme(Request $request){
        $themeData = $request->json('theme');
        $disciplineId = $request->json('disciplineId');

        $theme = new Theme();
        $theme->fillFromJson($themeData);
        $this->_disciplineManager->updateTheme($theme, $disciplineId);
    }

    public function deleteTheme($id){
        $this->_disciplineManager->deleteTheme($id);
    }

    public function getDisciplineProfilesIds($id){
        return json_encode($this->_disciplineManager->getDisciplineProfilesIds($id));
    }

    public function getTestsByDiscipline($disciplineId){
        return $this->_disciplineManager->getTestsByDiscipline($disciplineId);
    }
}
