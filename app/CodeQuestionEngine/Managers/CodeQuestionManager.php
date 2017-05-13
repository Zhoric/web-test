<?php
namespace CodeQuestionEngine;

use Auth;
use GivenAnswer;
use Repositories\UnitOfWork;
use App\Jobs\RunProgramJob;
use Queue;

class CodeQuestionManager
{


    /**
     * @var DockerInstance
     */
    private $dockerInstance;

    /**
     * @var \CodeFileManager
     */
    private $fileManager;
    /**
     * @var UnitOfWork
     */
    private $_uow;



    /**
     * @var \Language язык программирования
     */
    private $language;


    public function __construct(UnitOfWork $uow)
    {
        $this->_uow = $uow;
    }

    /**
     * @param $lang - Устанавливает язык программирования, за который отвечает данный менеджер
     * Инстанциирует необходимые зависимости для работы с конкретным языком программирования
     */
    public function setProgramLanguage($lang){
        $this->language = $lang;
        $this->fileManager = CodeFileManagerFactory::getCodeFileManager($lang);
    }

    /**
     * Запускает код на выполнение с входными параметрами, которые берутся из базы и заполняются преподавателем при
     * добавлении вопроса. Возвращает оценку студента
     * @param $code
     * @param object $program
     * @param $testResult
     * @return string оценка
     */
    public function runQuestionProgram($code,$program, $testResult)
    {
        $givenAnswer =  $this->createEmptyAnswerEntity($testResult);
        $this->prepareForRunning($code);
        $cases_count = $this->fileManager->createTestCasesFiles($program->getId());



        $this->run($cases_count,$program,$givenAnswer);

    }

    /**
     * Запускает код на выполнение с входными параметрами, которые передаются в виде массива. Возвращает результат работы программы
     * @param $code
     * @param array $paramSets
     * @return mixed
     * @throws \Exception
     */
    public function runQuestionProgramWithParamSets($code,array $paramSets){


            $this->prepareForRunning($code);

            $cases_count = $this->fileManager->createTestCasesFilesByParamsSetsArray($paramSets);


            //метод для админа, поэтому programId 0. Это значение несущественно
            $this->run($cases_count,0);

            $result = 'finished';

            /*$errors = $this->fileManager->getErrors();
            if($errors != ''){
                throw new \Exception($errors);
            }


           $result =  $this->fileManager->calculateMark($cases_count);
            $result.="\n";
            $result.= $this->fileManager->getResultsForCompare($cases_count);

            $this->fileManager->putLogInfo($result);
           */
            return $result;
    }


    /**
     * @param object $program
     * @param $cases_count
     */
    private function run($cases_count, $program,$givenAnswer){
        $dirName = $this->fileManager->getDirNameFromFullPath();
        $cache_dir = $this->fileManager->getCacheDirName();

        if($cases_count == 0){
            $this->fileManager->createInputFile();
            $result = $this->fileManager->createShellScript();
            $script_name = $result["scriptName"];
            $executeFileName = $result["executeFileName"];



            $command = "sh /opt/$cache_dir/$dirName/$script_name";

            $codeTask = new CodeTask($program->getId()
                ,$this->language
                ,$this->fileManager->getDirPath()
                ,$executeFileName
                ,\CodeTaskStatus::QueuedToExecute
                ,$program->getTimeLimit(),$program->getMemoryLimit(),1);
            $codeTask->store();

            Queue::push(new RunProgramJob($command,$codeTask));
            return;
        }

        for($i = 0; $i < $cases_count; $i++) {
            $result = $this->fileManager->createShellScriptForTestCase($program->getId(), $i);


            $script_name = $result["scriptName"];
            $executeFileName = $result["executeFileName"];

            $command = "sh /opt/$cache_dir/$dirName/$script_name";

            $codeTask = new CodeTask($program->getId()
                ,$this->language
                ,$this->fileManager->getDirPath()
                ,$executeFileName
                ,\CodeTaskStatus::QueuedToExecute
                ,$program->getTimeLimit(),$program->getMemoryLimit(),$cases_count,$i);

            $codeTask->store();


            Queue::push(new RunProgramJob($command,$codeTask));
        }


    }

    private function createEmptyAnswerEntity($testResult){
        //пустая сущность ответа на вопрос, потому что это костыль
        $givenAnswer = new GivenAnswer();
        $givenAnswer->setTestResult($testResult);
        $this->_uow->givenAnswers()->create($givenAnswer);
        $this->_uow->commit();
        return $givenAnswer;

    }
    private function prepareForRunning($code){
        $dirPath = $this->fileManager->createDir(Auth::user());
        $this->fileManager->setDirPath($dirPath);
        $this->fileManager->putCodeInFile($code);
        $this->fileManager->createLogFile();


    }




}