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

    public function getByNameAndProfilePaginated($disciplineName, $pageSize, $pageNumber, $profileId = null){
        return $this->_unitOfWork->disciplines()
            ->getByNameAndProfilePaginated($disciplineName,
                $pageSize, $pageNumber, $profileId = null);
    }

    public function addDiscipline(Discipline $discipline, array $profileIds){
        $this->_unitOfWork->disciplines()->create($discipline);
        $this->_unitOfWork->commit();

        //TODO: добавление связей с профилями
    }

    public function updateDiscipline(User $student){
        $this->_unitOfWork->disciplines()->update($student);
        $this->_unitOfWork->commit();

        //TODO: добавление связей с профилями
    }

    public function deleteDiscipline($disciplineId){
        $discipline = $this->_unitOfWork->disciplines()->find($disciplineId);

        if ($discipline != null){
            $this->_unitOfWork->disciplines()->delete($discipline);
            $this->_unitOfWork->commit();
        }
    }

}