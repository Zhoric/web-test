<?php

namespace Managers;

use Discipline;
use Repositories\UnitOfWork;
use Theme;

class DisciplineManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getAll(){
        return $this->_unitOfWork->groups()->all();
    }

    public function getByNameAndProfilePaginated($pageNum, $pageSize, $name, $profileId){
        return $this->_unitOfWork->disciplines()
            ->getByNameAndProfilePaginated($pageSize, $pageNum, $profileId, $name);
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
}