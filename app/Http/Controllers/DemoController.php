<?php

namespace App\Http\Controllers;

use App\models\Group;
use App\models\User;
use App\Process;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

    public function index(UserRepository $usersRepo){

    //   $container_id = exec("docker run -d -i -t baseimage-ssh /sbin/my_init",$output);

        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command_pattern = 'docker run -v $PWD/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init --skip-startup-files --quiet';
        $command = 'echo hello wold';
        //$result =  exec("$command_pattern $command",$output);

        //dd($result,$output);
       //$users = $em->entity('App\Models\User')->get(0);
        //$usersRepo->add(new Models\User());

        $user = new User();
        $user->id = 1;
        $user->fullName = "Вася1";
        $user->login = "vasya";
        $user->password = "12345";
        $user->role = 0;
        $user->groupId = 1;

        //$usersRepo->create($user);

        $vasyan = $usersRepo->where('fullName', 'like', '%Вас%');
        dd($vasyan);
    }

    public function editor(){

        return view('editor');
    }



}
