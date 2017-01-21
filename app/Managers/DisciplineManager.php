<?php

namespace Managers;

use Discipline;
use DisciplineInfoViewModel;
use Illuminate\Support\Facades\Auth;
use Repositories\UnitOfWork;
use Theme;
use Helpers\AuthHelper;
use UserRole;

class DisciplineManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    /**
     * Получение списка всех дисциплин для администратора/преподавателя.
     * Если текущий пользователь - преподаватель, будут возвращены только назначенные ему дисциплины.
     * @return array|mixed
     */
    public function getAll(){
        $lecturerId = null;
        $isLecturer = AuthHelper::isCurrentUserInRole($this->_unitOfWork, UserRole::Lecturer);

        if ($isLecturer) {
            $lecturerId = Auth::user()->getId();
            return $this->_unitOfWork->disciplines()->getForLecturer($lecturerId);
        } else {
            return $this->_unitOfWork->disciplines()->all();
        }
    }

    public function getByNameAndProfilePaginated($pageNum, $pageSize, $name, $profileId){

        $lecturerId = null;
        $isLecturer = AuthHelper::isCurrentUserInRole($this->_unitOfWork, UserRole::Lecturer);

        if ($isLecturer) {
            $lecturerId = Auth::user()->getId();
        }
        return $this->_unitOfWork->disciplines()
            ->getByNameAndProfilePaginated($pageSize, $pageNum, $profileId, $name, $lecturerId);
    }

    public function getByName($name){
        return $this->_unitOfWork->disciplines()->getByName($name);
    }

    public function addDiscipline(Discipline $discipline, array $profileIds){
        $this->_unitOfWork->disciplines()->create($discipline);
        $this->_unitOfWork->commit();

        $this->_unitOfWork->disciplines()
            ->setDisciplineProfiles($discipline->getId(), $profileIds);
        $this->_unitOfWork->commit();
    }

    public function updateDiscipline(Discipline $discipline, array $profileIds){
        $this->_unitOfWork->disciplines()->update($discipline);
        $this->_unitOfWork->commit();

        $this->_unitOfWork->disciplines()
            ->setDisciplineProfiles($discipline->getId(), $profileIds);
        $this->_unitOfWork->commit();
    }

    public function deleteDiscipline($disciplineId){
        $discipline = $this->_unitOfWork->disciplines()->find($disciplineId);

        if ($discipline != null){
            $this->_unitOfWork->disciplines()->delete($discipline);
            $this->_unitOfWork->commit();
        }
    }

    public function getDisciplineThemes($disciplineId){
        return $this->_unitOfWork->themes()->getByDiscipline($disciplineId);
    }

    public function addTheme(Theme $theme, $disciplineId){
        $discipline = $this->_unitOfWork->disciplines()->find($disciplineId);
        $theme->setDiscipline($discipline);

        $this->_unitOfWork->themes()->create($theme);
        $this->_unitOfWork->commit();
    }

    public function updateTheme(Theme $theme, $disciplineId){
        $discipline = $this->_unitOfWork->disciplines()->find($disciplineId);
        $theme->setDiscipline($discipline);

        $this->_unitOfWork->themes()->update($theme);
        $this->_unitOfWork->commit();
    }

    public function deleteTheme($id){
        $theme = $this->_unitOfWork->themes()->find($id);
        if ($theme != null){
            $this->_unitOfWork->themes()->delete($theme);
            $this->_unitOfWork->commit();
        }
    }

    public function getDisciplineProfilesIds($id){
        return $this->_unitOfWork->profiles()->getByDisciplineProfilesIds($id);
    }

    public function getTestsByDiscipline($disciplineId){
        return $this->_unitOfWork->tests()->getByDiscipline($disciplineId);
    }

    public function getTheme($id){
        return $this->_unitOfWork->themes()->find($id);
    }
    public function getDiscipline($id){
        return $this->_unitOfWork->disciplines()->find($id);
    }

    /*
     * Получение списка актуальных для текущего студента дисцпилин.
     */
    public function getActualDisciplinesForStudent($studentId, $currentSemester){
        $userGroup = $this->_unitOfWork->studentGroups()->getUserGroup($studentId);
        if ($userGroup == null || $userGroup->getGroup() == null){
            throw new \Exception("Невозможно определить группу студента!");
        }
        $groupId = $userGroup->getGroup()->getId();

        $discplines = $this->_unitOfWork->disciplines()
            ->getActualDisciplinesForGroup($groupId, $currentSemester);

        $discplinesVms = [];

        foreach ($discplines as $discpline){
            $testsCount = $this->getTestsCount($discpline);
            $passedCount = $this->getTestsPassedCount($discpline, $studentId);
            $disciplineVM = new DisciplineInfoViewModel($discpline, $testsCount, $passedCount);
            array_push($discplinesVms, $disciplineVM);
        }

        return $discplinesVms;
    }

    /*
    * Получение списка дисциплин, по которым у студента уже имеются результаты прохождения тестов.
    */
    public function getDisciplinesWhereTestsPassed($userId){
        return $this->_unitOfWork
            ->disciplines()
            ->getDisciplinesWhereTestsPassed($userId);
    }

    /**
     * Подсчёт общего количества тестов по дисциплине.
     * @param Discipline $discipline
     * @return int
     */
    private function getTestsCount(Discipline $discipline){
        $testsCount = 0;
        $tests = $this->_unitOfWork->tests()->getByDiscipline($discipline->getId());

        foreach ($tests as $test){
            if ($test->isActive()){
                $testsCount++;
            }
        }

        return $testsCount;
    }

    /**
     * Подсчёт количества пройденных тестов по дисциплине.
     * (пройденным считается тест, который студент пытался пройти хотя бы один раз.
     * @param Discipline $discipline
     * @return mixed
     */
    private function getTestsPassedCount(Discipline $discipline, $studentId){
        $count = $this
            ->_unitOfWork
            ->tests()
            ->getPassedTestsCount($studentId, $discipline->getId());

        return intval($count);
    }
}