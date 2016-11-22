<?php
namespace CodeQuestionEngine;
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 22.11.16
 * Time: 14:41
 */
class DockerEngine
{

    /**
     * Путь к корневой папке приложения
     */
    private $app_path;

    /**
     * Переменная, хранящая шаблон команды запуска виртуальной машины.
     * @var string
     */
    private $command_pattern;



    public function __construct()
    {
        $cache_dir = EngineGlobalSettings::CACHE_DIR;
        $memory_limit = EngineGlobalSettings::MEMORY_LIMIT;
        $image_name = EngineGlobalSettings::IMAGE_NAME;
        $this->command_pattern = "docker run -v $this->app_path/$cache_dir:/opt/$cache_dir -m $memory_limit $image_name /sbin/my_init --skip-startup-files --quiet";
        $this->app_path = app_path();
    }

    /**
     * Запуск консольной команды на виртуальной машине.
     * @param $command - текст команды
     * @return mixed - результат выполнения команды
     */
    public function run($command){

        error_reporting(E_ALL);
        ini_set('display_errors',1);

        exec("$this->command_pattern $command",$output);

        return $output;
    }




}