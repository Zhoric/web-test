<?php
namespace CodeQuestionEngine;
use User;
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 22.11.16
 * Time: 15:10
 */
class CodeFileManager
{

    private $app_path;
    public function __construct()
    {
        $this->app_path = app_path();
    }

    /**
     * Создает директорию cо следующим именем: ФИО юзера и текущий unix_time
     * @param \User $user
     * @return string - путь к созданной папке
     * @throws \Exception
     *
     */
    public function createDir(User $user)
    {
        try {
            $dirName = $user->getFirstname() . "_" .
                $user->getLastname() . "_" .
                $user->getPatronymic() . "_" .
                time();
            $cacheDir = EngineGlobalSettings::CACHE_DIR;

            $dirPath = "$this->app_path/$cacheDir/$dirName";
            mkdir($dirPath, 0777);
        }
        catch(\Exception $e){
            throw new \Exception("Не удалось создать директорию!");
        }
        return $dirPath;
    }



    public function getDirNameFromFullPath($dirPath){
        $splitted = explode("/",$dirPath);
        $name = array_pop($splitted);
        return $name;

    }

    public function getErrors($dirPath){
        $errors = file_get_contents("$dirPath/errors.txt");
        return $errors;
    }

    public function getResult($dirPath){
        $result = file_get_contents("$dirPath/result.txt");
        return $result;
    }

    public function putCodeInFile($code,$dirPath){
        $fp = fopen("$dirPath/code.c", "w");
        fwrite($fp, $code);
        fclose($fp);
    }

    /**
     * Метод берет базовый шелл-скрипт и создает на его основе скрипт,который запускает
     * на выполнение код, лежащий в уникальной папке пользователя.
     * @param $dirPath - путь к уникальной папке пользователя
     * @throws \Exception
     */
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

            $uniqueScript = fopen("$this->app_path/$cache_dir/$uniqueDirName/run.sh", "w");
            fwrite($uniqueScript, $command . $text);
            fclose($uniqueScript);


        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            throw new \Exception("Ошибка при создании скрипта: $msg");
        }

    }






}