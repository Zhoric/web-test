<?php
namespace CodeQuestionEngine;
use Mockery\CountValidator\Exception;
use Repositories\UnitOfWork;
use User;

class CCodeFileManager extends \CodeFileManagerBase
{

    public function createShellScript(){

        try {
            $cache_dir = $this->getCacheDirName();
            $sh_name = $this->getBaseShellScriptName();

            $shPath = "$this->app_path/$cache_dir/$sh_name";

            $baseShellScript = fopen($shPath, "r"); // открываем для чтения
            $text = fread($baseShellScript, filesize($shPath)); //читаем
            fclose($baseShellScript);

            $uniqueDirName = $this->getDirNameFromFullPath();

            $command = "cd /opt/$cache_dir/$uniqueDirName/\n";

            $filePath = "$this->app_path/$cache_dir/$uniqueDirName/$sh_name";

            $uniqueScript = fopen($filePath, "w");

            fwrite($uniqueScript, $command . $text);
            fclose($uniqueScript);

            $file = file_get_contents($filePath);
            $file = str_replace(EngineGlobalSettings::KEY_WORD_TO_PUT_RUN_INFO
                , "./a.out 1> $this->outputFileName < $this->inputFileName", $file);

            file_put_contents($filePath, $file);
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            throw new \Exception("Ошибка при создании скрипта: $msg");
        }

    }

    /**
     * Создает шелл-скрипты для для запуска программы для тестовых случаев
     * @param $programId
     * @param $casesCount
     * @throws \Exception
     */

    public function createShellScriptsForTestCases($programId, $casesCount){
        try {
            for($i = 0; $i < $casesCount ; $i++) {
                $this->CreateShellScriptForTestCase($programId,$i);
            }
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            throw new \Exception("Ошибка при создании скрипта: $msg");
        }
    }


}