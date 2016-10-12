<?php

namespace Managers;

use DisciplinePlan;
use Repositories\UnitOfWork;
use Studyplan;

class StudyPlanManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getPlan($id){
        return $this->_unitOfWork->disciplinePlans()->find($id);
    }

    public function create(Studyplan $studyplan){
        $this->_unitOfWork->studyPlans()->create($studyplan);
        $this->_unitOfWork->commit();
    }

    public function update(Studyplan $studyplan){
        $this->_unitOfWork->studyPlans()->update($studyplan);
        $this->_unitOfWork->commit();
    }

    public function delete($id){
        $studyPlan = $this->_unitOfWork->studyPlans()->find($id);
        if ($studyPlan != null){
            $this->_unitOfWork->studyPlans()->delete($studyPlan);
            $this->_unitOfWork->commit();
        }
    }

    public function addDisciplinePlan(DisciplinePlan $disciplinePlan){
        $this->_unitOfWork->disciplinePlans()->create($disciplinePlan);
        $this->_unitOfWork->commit();
    }

    public function updateDisciplinePlan(DisciplinePlan $disciplinePlan){
        $this->_unitOfWork->disciplinePlans()->update($disciplinePlan);
        $this->_unitOfWork->commit();
    }

    public function deleteDisciplinePlan($id){
        $disciplinePlan = $this->_unitOfWork->disciplinePlans()->find($id);
        if ($disciplinePlan != null){
            $this->_unitOfWork->disciplinePlans()->delete($disciplinePlan);
            $this->_unitOfWork->commit();
        }
    }
}