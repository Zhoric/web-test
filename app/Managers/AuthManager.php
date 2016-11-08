<?php


namespace Managers;


use Repositories\UnitOfWork;
use User;

class AuthManager
{


    protected $unitOfWork;
    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    public function createNewUser($json_credentials){
        $user = new User();
        $user->fillFromJson($json_credentials);
        $user->setPassword(bcrypt($user->getPassword()));
        $this->unitOfWork->users()->create($user);
        $this->unitOfWork->commit();

        return $this->unitOfWork->users()->findByEmail($user->getEmail());
    }

}