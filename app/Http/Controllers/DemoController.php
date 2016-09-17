<?php

namespace App\Http\Controllers;

use App\Process;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Mockery\CountValidator\Exception;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use Collective\Remote\RemoteServiceProvider;


class DemoController extends BaseController
{

    public function index(){


     //   $container_id = exec("docker run -d -i -t baseimage-ssh /sbin/my_init",$output);

        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command_pattern = 'docker run -v $PWD/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init --skip-startup-files --quiet';
        $command = 'echo hello wold';
        $result =  exec("$command_pattern $command",$output);

        dd($result,$output);



        return;



       // return view('welcome');
    }

    public function editor(){

        return view('editor');
    }



}
