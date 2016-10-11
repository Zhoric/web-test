<?php

namespace Managers;

use App\models\User;
use App\models\UserRole;
use Repositories\UnitOfWork;
use Repositories\UserRepository;

class GroupManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getAll(){
        return $this->_unitOfWork->groups()->all();
    }

    public function getProfileGroups($profileId){
        return $this->_unitOfWork->groups()->getGroupsByProfile($profileId);
    }
}