<?php

namespace App\Http\Controllers;

use Exception;
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
        try{
            $plan = $this->_studyPlanManager->getPlan($id);
            return $this->successJSONResponse($plan);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getPlansByProfile($profileId){
        try{
            $plans = $this->_studyPlanManager->getPlansByProfile($profileId);
            return $this->successJSONResponse($plans);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /* Пример валидных данных:
     * { "studyPlan": {"name":"Тестовый план"}, "profileId": 1}
     */
    public function create(Request $request){
        try{
            $planData = $request->json('studyPlan');
            $profileId = $request->json('profileId');
            $studyPlan = new Studyplan();
            $studyPlan->fillFromJson($planData);
            $this->_studyPlanManager->create($studyPlan, $profileId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function update(Request $request){
        try{
            $planData = $request->json('studyPlan');
            $profileId = $request->json('profileId');
            $studyPlan = new Studyplan();
            $studyPlan->fillFromJson($planData);
            $this->_studyPlanManager->update($studyPlan, $profileId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($id){
        try{
            $this->_studyPlanManager->delete($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    // Работа с планами дисциплин
    public function getPlanDisciplines($planId){
        try{
            $disciplines = $this->_studyPlanManager
                ->getPlanDisciplines($planId);
            return $this->successJSONResponse($disciplines);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getPlansDisciplinesByStudyplanAndNamePaginated(Request $request){
        try{
            $pageNum =  $request->query('page');
            $pageSize = $request->query('pageSize');
            $studyplanId = $request->query('studyplan');
            $name = $request->query('name');
            $paginationResult = $this->_studyPlanManager->getPlansDisciplinesByStudyplanAndNamePaginated($pageNum, $pageSize, $name, $studyplanId);

            return $this->successJSONResponse($paginationResult);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function addDisciplinePlan(Request $request){
        try{
            $planData = $request->json('disciplinePlan');
            $studyPlanId = $request->json('studyPlanId');
            $disciplineId = $request->json('disciplineId');

            $disciplinePlan = new DisciplinePlan();
            $disciplinePlan->fillFromJson($planData);
            $this->_studyPlanManager->createDisciplinePlan($disciplinePlan, $studyPlanId, $disciplineId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function updateDisciplinePlan(Request $request){
        try{
            $planData = $request->json('disciplinePlan');
            $studyPlanId = $request->json('studyPlanId');
            $disciplineId = $request->json('disciplineId');

            $disciplinePlan = new DisciplinePlan();
            $disciplinePlan->fillFromJson($planData);
            $this->_studyPlanManager->updateDisciplinePlan($disciplinePlan, $studyPlanId, $disciplineId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function deleteDisciplinePlan($id){
        try{
            $this->_studyPlanManager->deleteDisciplinePlan($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    // Работа с типами оценок
    public function getDisciplinePlanMarkTypes($disciplinePlanId){
        try{
            $markTypes = $this->_studyPlanManager
                ->getDisciplinePlanMarkTypes($disciplinePlanId);
            return $this->successJSONResponse($markTypes);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function addMarkType(Request $request){
        try{
            $markData = $request->json('markType');
            $disciplinePlanId = $request->json('disciplinePlanId');

            $markType = new MarkType();
            $markType->fillFromJson($markData);
            $this->_studyPlanManager->createMarkType($markType, $disciplinePlanId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function updateMarkType(Request $request){
        try{
            $markData = $request->json('markType');
            $disciplinePlanId = $request->json('disciplinePlanId');

            $markType = new MarkType();
            $markType->fillFromJson($markData);
            $this->_studyPlanManager->updateMarkType($markType, $disciplinePlanId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function deleteMarkType($id){
        try{
            $this->_studyPlanManager->deleteMarkType($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function linkMarkToTest(Request $request){
        try{
            $testId = $request->json('testId');
            $markTypeId = $request->json('markTypeId');
            $semester = $request->json('semester');

            $this->_studyPlanManager->linkMarkToTest($testId, $markTypeId, $semester);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

}
