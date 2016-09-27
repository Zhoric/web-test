<?php

namespace App\Http\Controllers;

use App\models\Group;
use App\models\User;
use App\models\UserRole;
use App\Process;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Managers\UserManager;
use Mockery\CountValidator\Exception;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use Collective\Remote\RemoteServiceProvider;
use ProAI\Datamapper\EntityManager;
use App\Models;
use Repositories;
use Repositories\UserRepository;

class DemoController extends BaseController
{
    public $_userManager;

    public function __construct(UserManager $userManager)
    {
        $this->_userManager = $userManager;
    }

    public function index(){

    //   $container_id = exec("docker run -d -i -t baseimage-ssh /sbin/my_init",$output);

        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command_pattern = 'docker run -v $PWD/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init --skip-startup-files --quiet';
        $command = 'echo hello wold';
        //$result =  exec("$command_pattern $command",$output);

        $user = new User();
        $user->id = 1;
        $user->fullName = "Вася1";
        $user->login = "vasya";
        $user->password = "12345";
        $user->role = 0;
        $user->groupId = 1;

        //$this->_userManager->addUser('Иван Петрович','vasya','123456',UserRole::Lecturer, 2013, 1);

        //$usersRepo->create($user);
        //$this->_userManager->updateUser(20,'Колян', 2055, 3);

        dd($this->_userManager->getStudents());


    }

    public function editor(){

        return view('editor');
    }



}
