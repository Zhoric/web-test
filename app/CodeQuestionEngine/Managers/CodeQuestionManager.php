<?php
namespace CodeQuestionEngine;

use Auth;
use Repositories\UnitOfWork;
use Language;

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
     * @var DockerManager
     */
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

        $this->fileManager = CodeFileManagerFactory::getCodeFileManager($lang);
        $this->dockerManager->setLanguage($lang);
        $this->dockerInstance = $this->dockerManager->getOrCreateInstance();
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

            $script_name = $this->fileManager->getBaseShellScriptName();
            $cache_dir = $this->fileManager->getCacheDirName();

            $this->dockerInstance->run("sh /opt/$cache_dir/$dirName/$script_name");
            $errors = $this->fileManager->getErrors();
            $result = $this->fileManager->getStudentResult();

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
     * @return string оценка
     */
    public function runQuestionProgram($code,$programId)
    {

        $dirPath = $this->fileManager->createDir(Auth::user());

        $this->fileManager->setDirPath($dirPath);
        $dirName = $this->fileManager->getDirNameFromFullPath();

        $this->fileManager->putCodeInFile($code);
        $cases_count = $this->fileManager->createTestCasesFiles($programId);
        $this->fileManager->createLogFile();

        $cache_dir = $this->fileManager->getCacheDirName();

        for($i = 0; $i < $cases_count; $i++) {
            $script_name = $this->fileManager->createShellScriptForTestCase($programId, $i);
            $this->dockerInstance->run("sh /opt/$cache_dir/$dirName/$script_name");
        }

        $result = $this->fileManager->calculateMark($cases_count);
        $this->fileManager->putLogInfo($result);

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
            $this->fileManager->setDirPath($dirPath);

            $dirName = $this->fileManager->getDirNameFromFullPath();

            $this->fileManager->putCodeInFile($code);
            $cases_count = $this->fileManager->createTestCasesFilesByParamsSetsArray($paramSets);

           // $this->fileManager->createShellScriptForTestCases($dirPath,$cases_count);
            $this->fileManager->createLogFile();
            $script_name = $this->fileManager->getBaseShellScriptName();
            $cache_dir = $this->fileManager->getCacheDirName();

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