<?php
namespace CodeQuestionEngine;

use Auth;
use Repositories\UnitOfWork;

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
     * Запускает код на выполнение с входными параметрами, которые берутся из базы и заполняются преподавателем при
     * добавлении вопроса. Возвращает оценку студента
     * @param $code
     * @param $programId
     * @return string оценка
     */
    public function runQuestionProgram($code,$programId)
    {
        $this->prepareForRunning($code);
        $cases_count = $this->fileManager->createTestCasesFiles($programId);
        $this->run($cases_count,$programId);
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

            $this->prepareForRunning($code);
            $cases_count = $this->fileManager->createTestCasesFilesByParamsSetsArray($paramSets);
            //метод для админа, поэтому programId 0. Это значение несущественно
            $this->run($cases_count,0);
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


    private function run($cases_count, $programId){
        $dirName = $this->fileManager->getDirNameFromFullPath();
        $cache_dir = $this->fileManager->getCacheDirName();
        for($i = 0; $i < $cases_count; $i++) {
            $script_name = $this->fileManager->createShellScriptForTestCase($programId, $i);
            $this->dockerInstance->run("sh /opt/$cache_dir/$dirName/$script_name");
        }
    }
    private function prepareForRunning($code){
        $dirPath = $this->fileManager->createDir(Auth::user());
        $this->fileManager->setDirPath($dirPath);
        $this->fileManager->putCodeInFile($code);
        $this->fileManager->createLogFile();
    }




}