<?php
namespace CodeQuestionEngine;

class DockerInstance
{

    /**
     * Путь к корневой папке приложения
     */
    private $app_path;

    /**
     * @var $container_id - айди докер-контейнера
     */
    private $container_id;

    public function __construct($container_id)
    {
        $this->container_id = $container_id;
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

        exec("docker exec $this->container_id $command", $output);

        return $output;
    }

    public function runAsync($command){
        $descriptorspec = array(
            0 => array('pipe', 'r'),
        );
        $pipes = array();

        $process = proc_open("docker exec $this->container_id $command",
            $descriptorspec,$pipes);


        return $process;

    }






}