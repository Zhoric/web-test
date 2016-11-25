<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 25.11.16
 * Time: 22:34
 */

namespace App\Http\Controllers;
use CodeQuestionEngine\CodeFileManager;
use CodeQuestionEngine\CodeQuestionManager;
use Illuminate\Http\Request;
use Exception;
use ParamsSet;
use Repositories\UnitOfWork;

class ProgramController extends Controller
{


    private $unitOfWork;
    private $codeManager;
    private $fileManager;
    public function __construct(UnitOfWork $unitOfWork,CodeQuestionManager $codeManager,CodeFileManager $fileManager)
    {
        $this->unitOfWork = $unitOfWork;
        $this->codeManager = $codeManager;
        $this->fileManager = $fileManager;
    }

    /*
     *   Добавление вопроса вместе с ответами
     *   Пример валидного JSON-запроса:
     *   {"question" : {"type": 1, "text": "Текст вопроса?", "complexity": 2, "time": 30},
     *    "theme" : 2,
     *    "answers" : [{"text":"Правильный ответ","isRight":true},
     *                 {"text":"Неправильный ответ","isRight":false}],
     *    "file" : "Содержимое файла в base64",
     *    "fileType" : "Тип файла",
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


            $paramsSetsObjects = [];
            foreach ($paramSets as $paramSet){
                $newParamSet = new ParamsSet();

                $newParamSet->setInput($paramSet['input']);
                $newParamSet->setExpectedOutput($paramSet['expectedOutput']);
                $paramsSetsObjects[] = $newParamSet;

            }


            $mark = $this->codeManager->runQuestionProgramWithParamSets($program,$paramsSetsObjects);

            return $this->successJSONResponse('Ваша оценка: '.$mark.'/100');
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }


}