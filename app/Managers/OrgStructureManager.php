<?php

namespace Managers;

use Repositories\UnitOfWork;
use Repositories\UserRepository;

/*
 * Огранизационная структура ВУЗа (институты, профили)
 */
class OrgStructureManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getInstitutes(){
        return $this->_unitOfWork->institutes()->all();
    }

    public function getInstituteProfiles($instituteId){
        return $this->_unitOfWork->profiles()->getByInstitute($instituteId);
    }
}