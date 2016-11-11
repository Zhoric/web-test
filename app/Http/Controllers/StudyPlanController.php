<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Managers\StudyPlanManager;
use MarkType;
use Studyplan;
use DisciplinePlan;

class StudyPlanController extends Controller
{
    private $_studyPlanManager;

    public function __construct(StudyPlanManager $studyPlanManager)
    {
        $this->_studyPlanManager = $studyPlanManager;
    }

    // Работа с учебными планами
    public function getPlan($id){
        return json_encode($this->_studyPlanManager->getPlan($id));
    }

    public function getPlansByProfile($profileId){
        return json_encode($this->_studyPlanManager->getPlansByProfile($profileId));
    }

    /* Пример валидных данных:
     * { "studyPlan": {"name":"Тестовый план"}, "profileId": 1}
     */
    public function create(Request $request){
        $planData = $request->json('studyPlan');
        $profileId = $request->json('profileId');
        $studyPlan = new Studyplan();
        $studyPlan->fillFromJson($planData);
        $this->_studyPlanManager->create($studyPlan, $profileId);
    }

    public function update(Request $request){
        $planData = $request->json('studyPlan');
        $profileId = $request->json('profileId');
        $studyPlan = new Studyplan();
        $studyPlan->fillFromJson($planData);
        $this->_studyPlanManager->update($studyPlan, $profileId);
    }

    public function delete($id){
        $this->_studyPlanManager->delete($id);
    }

    // Работа с планами дисциплин
    public function getPlanDisciplines($planId){
        return json_encode($this->_studyPlanManager
            ->getPlanDisciplines($planId));
    }

    public function getPlansDisciplinesByStudyplanAndNamePaginated(Request $request){
        $pageNum =  $request->query('page');
        $pageSize = $request->query('pageSize');
        $studyplanId = $request->query('studyplan');
        $name = $request->query('name');
        $paginationResult = $this->_studyPlanManager->getPlansDisciplinesByStudyplanAndNamePaginated($pageNum, $pageSize, $name, $studyplanId);

        return json_encode($paginationResult);
    }

    public function addDisciplinePlan(Request $request){
        $planData = $request->json('disciplinePlan');
        $studyPlanId = $request->json('studyPlanId');

        $disciplinePlan = new DisciplinePlan();
        $disciplinePlan->fillFromJson($planData);
        $this->_studyPlanManager->createDisciplinePlan($disciplinePlan, $studyPlanId);
    }

    public function updateDisciplinePlan(Request $request){
        $planData = $request->json('disciplinePlan');
        $studyPlanId = $request->json('studyPlanId');

        $disciplinePlan = new DisciplinePlan();
        $disciplinePlan->fillFromJson($planData);
        $this->_studyPlanManager->updateDisciplinePlan($disciplinePlan, $studyPlanId);
    }

    public function deleteDisciplinePlan($id){
        $this->_studyPlanManager->deleteDisciplinePlan($id);
    }

    // Работа с типами оценок
    public function getDisciplinePlanMarkTypes($disciplinePlanId){
        return json_encode($this->_studyPlanManager
            ->getDisciplinePlanMarkTypes($disciplinePlanId));
    }

    public function addMarkType(Request $request){
        $markData = $request->json('markType');
        $disciplinePlanId = $request->json('disciplinePlanId');

        $markType = new MarkType();
        $markType->fillFromJson($markData);
        $this->_studyPlanManager->createMarkType($markType, $disciplinePlanId);
    }

    public function updateMarkType(Request $request){
        $markData = $request->json('markType');
        $disciplinePlanId = $request->json('disciplinePlanId');

        $markType = new MarkType();
        $markType->fillFromJson($markData);
        $this->_studyPlanManager->updateMarkType($markType, $disciplinePlanId);
    }

    public function deleteMarkType($id){
        $this->_studyPlanManager->deleteMarkType($id);
    }

    public function linkMarkToTest(Request $request){
        $testId = $request->json('testId');
        $markTypeId = $request->json('markTypeId');
        $semester = $request->json('semester');

        $this->_studyPlanManager->linkMarkToTest($testId, $markTypeId, $semester);
    }

}
