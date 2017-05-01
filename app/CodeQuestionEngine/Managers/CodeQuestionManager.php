<?php
namespace CodeQuestionEngine;

use Auth;
use Repositories\UnitOfWork;
use Language;


class CodeQuestionManager
{


    private $dockerInstance;

    /**
     * @var \CodeFileManagerBase
     */
    private $fileManager;
    private $_uow;
    private $dockerManager;


    public function __construct(UnitOfWork $uow, DockerManager $dockerManager)
    {
        $this->dockerManager = $dockerManager;
        $this->_uow = $uow;
    }

    /**
     * @param $lang - Устанавливает язык программирования, за который отвечает данный менеджер
     * Инстанциирует необходимые зависимости для работы с конкретным языком программирования
     */
    public function setProgramLanguage($lang){

        $this->fileManager = $this->getFileManagerInstanceByLanguage($lang);
        $this->dockerInstance = $this->dockerManager->getOrCreateInstance($lang);
    }

    /**
     * Запускает код на выполнение, возвращает результаты выполнения.
     * @param string $code
     * @return string
     */
    public function run($code){
        try {

            $dirPath = $this->fileManager->createDir(Auth::user());

            $this->fileManager->setDirPath($dirPath);
            $dirName = $this->fileManager->getDirNameFromFullPath();

            $this->fileManager->putCodeInFile($code);
            $this->fileManager->createEmptyInputFile();
            $this->fileManager->createShellScript();

            $script_name = EngineGlobalSettings::SHELL_SCRIPT_NAME;
            $cache_dir = EngineGlobalSettings::CACHE_DIR;

            $this->dockerInstance->run("sh /opt/$cache_dir/$dirName/$script_name");
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
    public function runQuestionProgram($code,$programId)
    {

        $dirPath = $this->fileManager->createDir(Auth::user());
        $this->fileManager->setDirPath($dirPath);
        $dirName = $this->fileManager->getDirNameFromFullPath();

        $this->fileManager->putCodeInFile($code);
        $cases_count = $this->fileManager->createTestCasesFiles($programId);

        $this->fileManager->createShellScriptForTestCases($programId, $cases_count);
        $this->fileManager->createLogFile();

        $script_name = EngineGlobalSettings::SHELL_SCRIPT_NAME;
        $cache_dir = EngineGlobalSettings::CACHE_DIR;

        $this->dockerInstance->run("sh /opt/$cache_dir/$dirName/$script_name");

        $result = $this->fileManager->calculateMark($cases_count);
        $this->fileManager->putLogInfo($result);

        return $result;

    }

    private function getFileManagerInstanceByLanguage($lang){

        switch($lang){
            case Language::C: {
                return new CCodeFileManager($this->_uow);
            }break;
        }
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
            $this->fileManager->setDirPath($dirPath);

            $dirName = $this->fileManager->getDirNameFromFullPath();

            $this->fileManager->putCodeInFile($code);
            $cases_count = $this->fileManager->createTestCasesFilesByParamsSetsArray($paramSets);

           // $this->fileManager->createShellScriptForTestCases($dirPath,$cases_count);
            $this->fileManager->createLogFile();
            $script_name = EngineGlobalSettings::SHELL_SCRIPT_NAME;
            $cache_dir = EngineGlobalSettings::CACHE_DIR;
            $this->dockerInstance->run("sh /opt/$cache_dir/$dirName/$script_name");
            $errors = $this->fileManager->getErrors();
            if($errors != ''){
                throw new \Exception($errors);
            }
            $result =  $this->fileManager->calculateMark($cases_count);
            $result.="\n";
            $result.= $this->fileManager->getResultsForCompare($cases_count);

            $this->fileManager->putLogInfo($result);

        return $result;

    }

}