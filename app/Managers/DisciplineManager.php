<?php

namespace Managers;

use Discipline;
use Repositories\UnitOfWork;

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

}