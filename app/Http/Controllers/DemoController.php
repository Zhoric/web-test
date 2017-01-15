<?php

namespace App\Http\Controllers;



use App\Process;


use CodeQuestionEngine\CodeFileManager;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Managers\ProfileManager;
use Managers\UISettingsCacheManager;
use Repositories\UnitOfWork;
use Illuminate\Http\Request;
use CodeQuestionEngine\CodeQuestionManager;

class DemoController extends BaseController
{
    private $_uow;
    private $app_path;
    private $manager;
    private $fileManager;


    public function __construct(UnitOfWork $uow, CodeFileManager $fileManager, CodeQuestionManager $manager)
    {
        $this->_uow = $uow;
        $this->fileManager = $fileManager;
        $this->manager = $manager;
        $this->app_path = app_path();
    }

    public function auth(){


    }

    public function docker(){
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command_pattern = 'docker run -v $PWD/temp_cache:/opt/temp_cache -m 50M baseimage-ssh /sbin/my_init --skip-startup-files --quiet';
        $command = 'echo hello wold';
        $result =  exec("$command_pattern $command",$output);
        dd($result,$output);
        return;

    }

    


    public function editor(){
        return view('editor');
    }

    public function receiveCode(Request $request){
        $code = $request->input('code');

      //  $result = $this->manager->runQuestionProgram($code,1);

        $result = $this->manager->run($code);
        return $result;

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
