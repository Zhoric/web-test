<?php

namespace Managers;

use App\models\Discipline;
use Repositories\DisciplineRepository;
use Repositories\UserRepository;

class DisciplineManager
{
    private $_userRepo;
    private $_disciplineRepo;

    public function __construct(UserRepository $userRepo,
                                DisciplineRepository $disciplineRepo)
    {
        $this->_userRepo = $userRepo;
        $this->_disciplineRepo = $disciplineRepo;
    }

    public function addDiscipline($name, $fullTimeStart, $extramuralStart)
    {
        $discipline = new Discipline();
        $discipline->name = $name;
        $discipline->fullTimeStart = $fullTimeStart;
        $discipline->extramuralStart = $extramuralStart;

        $this->_disciplineRepo->create($discipline);
    }

    public function updateDiscipline($id, $name, $ftStart, $emStart)
    {
        $discipline = new Discipline();
        $discipline->id = $id;
        $discipline->name = $name;
        $discipline->fullTimeStart = $ftStart;
        $discipline->extramuralStart = $emStart;

        $this->_disciplineRepo->update($discipline);
    }

    public function deleteDiscipline($id)
    {
        $discipline = $this->_disciplineRepo->find($id);
        $this->_disciplineRepo->delete($discipline);
    }

    public function getLecturerWithDisciplines($id)
    {
        return $this->_userRepo->findWith($id, 'disciplines');
    }

    public function getDiscipline($id)
    {
        return $this->_disciplineRepo->findWith($id, 'profiles');
    }

    public function updateLecturerDisciplines($id, $disciplinesIds)
    {
        $this->_disciplineRepo->updateLecturerDisciplines($id, $disciplinesIds);
    }

    public function updateDisciplinesProfiles($disciplineId, $profilesIds)
    {
        $this->_disciplineRepo->updateDisciplineProfiles($disciplineId, $profilesIds);
    }
}