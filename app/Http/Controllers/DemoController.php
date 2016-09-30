<?php

namespace App\Http\Controllers;


use App\Process;

use Illuminate\Routing\Controller as BaseController;
use App\models\UserRole;
use Managers\DisciplineManager;
use Managers\GroupManager;
use Managers\ProfileManager;
use Managers\UserManager;
use Repositories\UserRepository;

class DemoController extends BaseController
{
    public $_userManager;
    public $_groupManager;
    public $_disciplineManager;
    public $_profileManager;

    public function __construct(UserManager $userManager,
                                GroupManager $groupManager,
                                DisciplineManager $disciplineManager,
                                ProfileManager $profileManager)
    {
        $this->_userManager = $userManager;
        $this->_groupManager = $groupManager;
        $this->_disciplineManager = $disciplineManager;
        $this->_profileManager = $profileManager;
    }



    public function docker(){
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command_pattern = 'docker run -v $PWD/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init --skip-startup-files --quiet';
        $command = 'echo hello wold';
        $result =  exec("$command_pattern $command",$output);


        dd($result);
    }
    public function index(){




        $user = $this->_userManager->getUserByRememberToken('1','1234');

        dd($user);



       // $this->_userManager->addUser('Иван Петрович','vasya','123456',UserRole::Lecturer, 2013, 1);


      //  $this->_userManager->updateUser(20,'Колян', 2055, 3);

       // dd($this->_groupManager->addGroup(0,'ИСб',4,true,1));
       // dd($this->_disciplineManager->getLecturerWithDisciplines(1));


    }

    public function getProfiles(){

        return json_encode($this->_profileManager->getAllProfiles());
    }



}
