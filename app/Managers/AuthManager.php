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

    private function checkIfEmailExists($email){
        if(!empty($this->unitOfWork->users()->findByEmail($email))){
            return true;
        }
        return false;
    }

    public function createNewUser($json_credentials){
        $user = new User();
        $user->fillFromJson($json_credentials);

        if($this->checkIfEmailExists($user->getEmail())){
           throw new \Exception('Пользователь с данным email уже существует!');
        }


        $user->setPassword(bcrypt($user->getPassword()));
        $this->unitOfWork->users()->create($user);
        $this->unitOfWork->commit();

        return $this->unitOfWork->users()->findByEmail($user->getEmail());
    }

    public function checkIfUserActive($email){

        $user = $this->unitOfWork->users()->findByEmail($email);
        if($user->getActive()){
            return true;
        }
        return false;
    }




}