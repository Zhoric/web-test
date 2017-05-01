<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 25.11.16
 * Time: 22:34
 */

namespace App\Http\Controllers;
use CodeFileManager;
use CodeQuestionEngine\CodeQuestionManager;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Lang;
use ParamsSet;
use ProgramViewModel;
use Repositories\UnitOfWork;

class ProgramController extends Controller
{


    private $unitOfWork;
    private $codeManager;
    private $fileManager;
    public function __construct(UnitOfWork $unitOfWork, CodeQuestionManager $codeManager, CodeFileManager $fileManager)
    {
        $this->unitOfWork = $unitOfWork;
        $this->codeManager = $codeManager;
        $this->fileManager = $fileManager;
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

            $this->codeManager->setProgramLanguage(\Language::C);

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


            return $this->successJSONResponse('Ваша оценка: '.$mark);
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