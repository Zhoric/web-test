<?php
namespace CodeQuestionEngine;

use Auth;


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


    public function __construct(DockerEngine $dockerEngine, CodeFileManager $fileManager)
    {
        $this->dockerEngine = $dockerEngine;
        $this->fileManager = $fileManager;
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





}