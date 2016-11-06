<?php

namespace App\Http\Controllers;



use App\Process;

use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Managers\DisciplineManager;
use Managers\GroupManager;
use Managers\ProfileManager;
use Managers\UserManager;
use Repositories\UnitOfWork;
use Repositories\UserRepository;
use User;
use Auth;

class DemoController extends BaseController
{
    private $_uow;

    public function __construct(UnitOfWork $uow)
    {
        $this->_uow = $uow;
    }

    public function docker(){
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command_pattern = 'docker run -v $PWD/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init --skip-startup-files --quiet';
        $command = 'echo hello wold';
        $result =  exec("$command_pattern $command",$output);

        dd($result);
    }

    public function auth(){
        $user = Auth::user();
        dd($user);
    }

    public function index(){


       // $user = $this->_userManager->getUserByRememberToken('1','1234');

       // $this->_userManager->addUser('Иван Петрович','vasya','123456',UserRole::Lecturer, 2013, 1);


      //  $this->_userManager->updateUser(20,'Колян', 2055, 3);

       // dd($this->_groupManager->addGroup(0,'ИСб',4,true,1));
       // dd($this->_disciplineManager->getLecturerWithDisciplines(1));


        //return new JsonResponse($users);
        return json_encode($this->_uow->getUsersRepo()->all());
    }
}
