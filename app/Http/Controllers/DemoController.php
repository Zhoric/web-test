<?php

namespace App\Http\Controllers;



use App\Process;


use CodeQuestionEngine\CodeFileManager;
use Illuminate\Routing\Controller as BaseController;
use Managers\ProfileManager;
use Repositories\UnitOfWork;
use Illuminate\Http\Request;
use CodeQuestionEngine\CodeQuestionManager;
use Auth;

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


    public function editor(){
        return view('editor');
    }

    public function receiveCode(Request $request){
        $code = $request->input('code');

        $result = $this->manager->runQuestionProgram($code,1);
        return $result;

    }



}
