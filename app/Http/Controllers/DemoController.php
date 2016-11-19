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
use Illuminate\Http\Request;
use Repositories\UserRepository;
use User;
use Auth;

class DemoController extends BaseController
{
    private $_uow;
    private $app_path;

    public function __construct(UnitOfWork $uow)
    {
        $this->_uow = $uow;
        $this->app_path = app_path();
    }

    public function docker(){

        error_reporting(E_ALL);
        ini_set('display_errors',1);

        $command_pattern = "docker run -v $this->app_path/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init --skip-startup-files --quiet";
        $command = "sh /opt/temp_cache/run.sh";

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

    public function receiveCode(Request $request){
           $code = $request->input('code');
           $this->putCodeInFile($code);
           $this->runOnDocker();
           $errors = $this->getErrors();
           $result = $this->getResult();

           $msg = $errors.' '.$result;
           return $msg;
    }

    public function putCodeInFile($code){
        $fp = fopen("$this->app_path/temp_cache/file.c", "w");
        fwrite($fp, $code);
        fclose($fp);
    }
    

    public function runOnDocker(){
        error_reporting(E_ALL);
        ini_set('display_errors',1);

        $command_pattern = "docker run -v $this->app_path/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init --skip-startup-files --quiet";
        $command = "sh /opt/temp_cache/run.sh";

        exec("$command_pattern $command",$output);

    }

    public function getErrors(){
        $errors = file_get_contents("$this->app_path/temp_cache/errors.txt");
        return $errors;
    }

    public function getResult(){
        $result = file_get_contents("$this->app_path/temp_cache/result.txt");
        return $result;
    }

    public function index(){

    }
}
