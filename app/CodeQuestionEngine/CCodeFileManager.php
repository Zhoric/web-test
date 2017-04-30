<?php
namespace CodeQuestionEngine;
use Mockery\CountValidator\Exception;
use Repositories\UnitOfWork;
use User;

class CCodeFileManager extends \CodeFileManagerBase
{


    public function putCodeInFile($code,$dirPath){
        $fp = fopen("$dirPath/code.c", "w");
        fwrite($fp, $code);
        fclose($fp);
    }


    public function createShellScript($dirPath){

        try {
            $cache_dir = EngineGlobalSettings::CACHE_DIR;
            $sh_name = EngineGlobalSettings::SHELL_SCRIPT_NAME;
            $shPath = "$this->app_path/$cache_dir/$sh_name";

            $baseShellScript = fopen($shPath, "r"); // открываем для чтения
            $text = fread($baseShellScript, filesize($shPath)); //читаем
            fclose($baseShellScript);
            $uniqueDirName = $this->getDirNameFromFullPath($dirPath);
            $command = "cd /opt/$cache_dir/$uniqueDirName/\n";
            $filePath = "$this->app_path/$cache_dir/$uniqueDirName/run.sh";
            $uniqueScript = fopen($filePath, "w");
            fwrite($uniqueScript, $command . $text);
            fclose($uniqueScript);

            $file = file_get_contents($filePath);
            $file = str_replace('run', './a.out 1> result.txt < input.txt', $file);
            file_put_contents($filePath, $file);
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            throw new \Exception("Ошибка при создании скрипта: $msg");
        }

    }


    public function createShellScriptForTestCases($dirPath, $programId, $casesCount){
        try {

            $cache_dir = EngineGlobalSettings::CACHE_DIR;
            $sh_name = EngineGlobalSettings::SHELL_SCRIPT_NAME;
            $shPath = "$this->app_path/$cache_dir/$sh_name";

            $baseShellScript = fopen($shPath, "r"); // открываем для чтения
            $text = fread($baseShellScript, filesize($shPath)); //читаем
            fclose($baseShellScript);
            $uniqueDirName = $this->getDirNameFromFullPath($dirPath);

            for($i = 0; $i < $casesCount ; $i++) {

                $command = "cd /opt/$cache_dir/$uniqueDirName/\n";

                $filePath = "$this->app_path/$cache_dir/$uniqueDirName/run$i.sh";
                $uniqueScript = fopen($filePath, "w");
                fwrite($uniqueScript, $command . $text);
                fclose($uniqueScript);

                $file = file_get_contents($filePath);
                $command = '';
                $command .= "./a.out 1> student_result_$i.txt < test_input_$i.txt\n";
                $file = str_replace('run', $command, $file);
                file_put_contents($filePath, $file);
            }

        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            throw new \Exception("Ошибка при создании скрипта: $msg");
        }
    }


}