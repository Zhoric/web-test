<?php

namespace App\Http\Controllers;



use App\Jobs\RunProgramJob;
use App\Process;




use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Managers\TestResultManager;
use Managers\UISettingsCacheManager;
use Queue;
use Repositories\UnitOfWork;
use Illuminate\Http\Request;
use TaskStatesManager;
use CodeQuestionManagerProxy;

class DemoController extends BaseController
{
    private $_uow;
    private $app_path;
    private $manager;
    private $fileManager;
    private $dockerManager;
    private $taskStatesManager;
    private $testResultManager;



    public function __construct(UnitOfWork $uow,TestResultManager $testResultManager,  CodeQuestionManagerProxy $manager)
    {
        $this->_uow = $uow;
        $this->fileManager = CodeFileManagerFactory::getCodeFileManager(\Language::C);
        $this->manager = $manager;
        $this->app_path = app_path();

        $this->testResultManager = $testResultManager;
    }

    public function auth(){


    }


    public function test(){

    }

    public function docker(){

        $program = $this->_uow->programs()->find(1);

       // $this->manager->setProgramLanguage($program->getLang());
        $testResult = $this->_uow->testResults()->find(1);
        $question = $this->_uow->questions()->find(1);


        $paramSets = $this->_uow->paramsSets()->getByProgram(1);



       $result =  $this->manager->runQuestionProgram($program->getTemplate(), $program,$paramSets, $testResult, $question);

       return $result;
        for($i = 0; $i < 1; $i++) {
           $this->manager->runQuestionProgram($program->getTemplate(), $program,$paramSets, $testResult, $question);
            sleep(1);
        }


        dd("done");





        $command = "ps aux";
        $this->dockerManager->setLanguage(\Language::C);
        $dockerInstance = $this->dockerManager->getOrCreateInstance();
        $result = $dockerInstance->run($command);

        $result = $dockerInstance->getProcessInfo("c_output_1_0.out");


        dd($result);

        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command = "ps aux";
        exec("docker exec b178c3393937f51af504a6f1d13d49784f24d8875c65346ff70d5f962e346569 $command", $output);

        dd($output);


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


        $command = 'sh /opt/temp_cache/Петр_Петров_Перович_1493669336/run0.sh';
        for($i = 0; $i < 1; $i ++) {

            $queue = Queue::push(new RunProgramJob(\Language::C,$command));


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
