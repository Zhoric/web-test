<?php
namespace CodeQuestionEngine;

use Auth;
use Repositories\UnitOfWork;


/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 22.11.16
 * Time: 14:57
 */
class CodeQuestionManager
{


    private $dockerEngine;
    private $fileManager;
    private $_uow;


    public function __construct(DockerEngine $dockerEngine, CodeFileManager $fileManager,UnitOfWork $uow)
    {
        $this->dockerEngine = $dockerEngine;
        $this->fileManager = $fileManager;
        $this->_uow = $uow;
    }

    /**
     * Запускает код на выполнение, возвращает результаты выполнения.
     * @param string $code
     * @return string
     */
    public function run($code){

            try {
                $dirPath = $this->fileManager->createDir(Auth::user());
                $dirName = $this->fileManager->getDirNameFromFullPath($dirPath);

                $this->fileManager->putCodeInFile($code, $dirPath);
                $this->fileManager->createEmptyInputFile($dirPath);
                $this->fileManager->createShellScript($dirPath);

                $script_name = EngineGlobalSettings::SHELL_SCRIPT_NAME;
                $cache_dir = EngineGlobalSettings::CACHE_DIR;

                $this->dockerEngine->run("sh /opt/$cache_dir/$dirName/$script_name");
                $errors = $this->fileManager->getErrors($dirPath);
                $result = $this->fileManager->getStudentResult($dirPath);

                $msg = $errors . ' ' . $result;
            } catch (\Exception $e) {
                return $e->getMessage();
            }

        return $msg;
    }

    /**
     * Запускает код на выполнение с входными параметрами, которые берутся из базы и заполняются преподавателем при
     * добавлении вопроса. Возвращает оценку студента
     * @param $code
     * @param $programId
     * @return mixed
     */
    public function runQuestionProgram($code,$programId){
          $dirPath = $this->fileManager->createDir(Auth::user());
            $dirName = $this->fileManager->getDirNameFromFullPath($dirPath);

            $this->fileManager->putCodeInFile($code, $dirPath);
            $cases_count = $this->fileManager->createTestCasesFiles($programId,$dirPath);

            $this->fileManager->createShellScriptForTestCases($dirPath,$cases_count);

            $script_name = EngineGlobalSettings::SHELL_SCRIPT_NAME;
            $cache_dir = EngineGlobalSettings::CACHE_DIR;
            $this->dockerEngine->run("sh /opt/$cache_dir/$dirName/$script_name");
           $result =  $this->fileManager->calculateMark($dirPath,$cases_count);

        return $result;

    }




    /**
     * Запускает код на выполнение с входными параметрами, которые передаются в виде массива. Возвращает результат работы программы
     * @param $code
     * @param array $paramSets
     * @return mixed
     * @throws \Exception
     */
    public function runQuestionProgramWithParamSets($code,array $paramSets){

            $dirPath = $this->fileManager->createDir(Auth::user());
            $dirName = $this->fileManager->getDirNameFromFullPath($dirPath);

            $this->fileManager->putCodeInFile($code, $dirPath);
            $cases_count = $this->fileManager->createTestCasesFilesByParamsSetsArray($paramSets,$dirPath);

            $this->fileManager->createShellScriptForTestCases($dirPath,$cases_count);

            $script_name = EngineGlobalSettings::SHELL_SCRIPT_NAME;
            $cache_dir = EngineGlobalSettings::CACHE_DIR;
            $this->dockerEngine->run("sh /opt/$cache_dir/$dirName/$script_name");
            $errors = $this->fileManager->getErrors($dirPath);
            if($errors != ''){
                throw new \Exception($errors);
            }
            $result =  $this->fileManager->calculateMark($dirPath,$cases_count);
            $result.="\n";
            $result.= $this->fileManager->getResultsForCompare($dirPath,$cases_count);

        return $result;

    }















}