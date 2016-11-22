<?php

namespace App\Http\Controllers;



use App\Process;



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


    public function __construct(UnitOfWork $uow, CodeQuestionManager $manager)
    {
        $this->_uow = $uow;
        $this->manager = $manager;
        $this->app_path = app_path();
    }

    public function auth(){
        $res = $this->_uow->paramsSets()->all();

        dd($res);
    }


    public function editor(){
        return view('editor');
    }

    public function receiveCode(Request $request){
        $code = $request->input('code');
        $result = $this->manager->run($code);
        return $result;
    }



}
