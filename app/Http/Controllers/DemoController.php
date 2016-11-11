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

    public function editor(){
        return view('editor');
    }

    public function index(){

    }
}
