<?php

namespace App\Http\Controllers;



use App\Jobs\TestJob;
use App\Process;


use CodeQuestionEngine\CCodeFileManager;
use CodeQuestionEngine\DockerInstance;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Managers\ProfileManager;
use Managers\UISettingsCacheManager;
use Queue;
use CodeQuestionEngine\CodeTask;
use Repositories\DockerInfoRepository;
use Repositories\UnitOfWork;
use Illuminate\Http\Request;
use CodeQuestionEngine\CodeQuestionManager;
use CodeQuestionEngine\EngineGlobalSettings;

class DemoController extends BaseController
{
    private $_uow;
    private $app_path;
    private $manager;
    private $fileManager;
    private $dockerInstance;



    public function __construct(UnitOfWork $uow, DockerInstance $dockerEngine, CCodeFileManager $fileManager, CodeQuestionManager $manager)
    {
        $this->_uow = $uow;
        $this->fileManager = $fileManager;
        $this->manager = $manager;
        $this->app_path = app_path();
        $this->dockerInstance = $dockerEngine;
    }

    public function auth(){


    }


    public function test(){

    }

    public function docker(){

        $app_path = app_path();
        $cache_dir = EngineGlobalSettings::CACHE_DIR;

        $dirPath = "$app_path/$cache_dir/code";
        file_get_contents("$dirPath/test.c");

        $start_time = microtime(true);

        $command_pattern = "docker run -d -v $this->app_path/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init";

        dd($command_pattern);

        $container_id =  exec("$command_pattern",$output);


        $command_pattern = "sh /opt/temp_cache/code/run.sh";

        $descriptorspec = array(
            0 => array('pipe', 'r'),
        );

        $process = proc_open("docker exec $container_id $command_pattern",
            $descriptorspec,$pipes);
        dd($process);


        $current_time = microtime(true);


        while($current_time - $start_time < 10){
            $metainfo = proc_get_status($process);

            if($metainfo["running"] === false){

                return "no overtime";
                break;
            }
            sleep(1);

            $current_time = microtime(true);
        }
        $metainfo = proc_get_status($process);
                   $pid = $metainfo['pid'];
                  $sigterm = 9;
                   posix_kill ( $pid, $sigterm );

       // $command_pattern = "docker stop $container_id";

       // exec($command_pattern,$output);

        return "overtime";



    }


    public function connect(){

        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command_pattern = "docker run -d -v $this->app_path/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init";
        $command = 'echo hello world';

        $container_id =  exec("$command_pattern",$output);

        $container_id = substr($container_id,0,7);

        $command_pattern = "docker stop $container_id";

        exec($command_pattern,$output);

        $container_id = substr($container_id, 0, 5);
        $command_pattern = "docker exec $container_id $command";


        $result = exec($command_pattern,$external);



    }


    public function docker_test(){

    }


    public function editor(){
        return view('editor');
    }

    public function receiveCode(){


        for($i = 0; $i < 1; $i ++) {

            $queue = Queue::push(new TestJob());


        }
        return $queue;
    }

    /**
     * Установка настроек. Пример тела запроса:
     * {"settings": {"hello":"hello world!", "test": 666}}
     * @param Request $request
     * @throws \Exception
     */
    public function setSettings(Request $request){
        $settings = $request->json('settings');
        $currentUser = Auth::user();

        if (!isset($currentUser)){
            throw new \Exception('Для данного действия необходима авторизация!');
        }

        $userId = $currentUser->getId();
        $settMan = app()->make(UISettingsCacheManager::class);
        $settMan->setValues($userId, $settings);
    }




    /**
     * Получение настроек. Пример тела запроса:
     * {"settings":["hello","test"]}
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function getSettings(Request $request){
        $settingKeys = $request->json()->get("settings");
        $currentUser = Auth::user();

        if (!isset($currentUser)){
            throw new \Exception('Для данного действия необходима авторизация!');
        }

        $userId = $currentUser->getId();
        $settMan = app()->make(UISettingsCacheManager::class);
        $settings = $settMan->getValues($userId, $settingKeys);
        return json_encode($settings);
    }

}
