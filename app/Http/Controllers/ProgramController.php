<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use ParamsSet;
use ProgramViewModel;
use Repositories\UnitOfWork;
use CodeQuestionManagerProxy;

class ProgramController extends Controller
{


    private $unitOfWork;
    private $codeManagerProxy;
    public function __construct(UnitOfWork $unitOfWork, CodeQuestionManagerProxy $codeQuestionManagerProxy)
    {
        $this->unitOfWork = $unitOfWork;
        $this->codeManagerProxy = $codeQuestionManagerProxy;
    }

    /*
     *   Запуск программы на выполнение.
     *   Пример валидного JSON-запроса:
     *   {
     *    "program" : "Текст программы",
     *    "paramSets" : [{"input":"Входные параметры1", "expectedOutput":"Выходной параметр1"},
     *      {"input":"Входные параметры2", "expectedOutput":"Выходной параметр2"}],
     *    }
     */
    public function run(Request $request){
        try{



            $program = $request->json('program');

             $program = json_decode($program);



            $paramSets = (array) $request->json('paramSets');

            $language = $request->json('lang');
            $timeLimit = $request->json('timeLimit');
            $memoryLimit = $request->json('memoryLimit');

            if($timeLimit == null){
                $timeLimit = 1;
            }
            if($memoryLimit == null){
                $memoryLimit = 100;
            }


            if(empty($language)){
                $language = \Language::C;
            }
            else {
                $language = \Language::getLanguageByAlias($language);
            }


            $paramsSetsObjects = [];
            foreach ($paramSets as $paramSet){
                $newParamSet = new ParamsSet();

                $newParamSet->setInput($paramSet['input']);
                $newParamSet->setExpectedOutput($paramSet['expectedOutput']);
                $paramsSetsObjects[] = $newParamSet;

            }


            $result = $this->codeManagerProxy->runProgram($program,$language,$timeLimit,$memoryLimit,$paramsSetsObjects);

            return $this->successJSONResponse('Результат: '.$result);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getByQuestion($id){
        try{
            $program = $this->unitOfWork->programs()->getByQuestion($id);
            if (isset($program)){
                $paramSets = $this->unitOfWork->paramsSets()->getByProgram($program->getId());
                return $this->successJSONResponse(new ProgramViewModel($program, $paramSets));
            } else {
                throw new Exception('Данный вопрос не содержит программу!');
            }
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

}