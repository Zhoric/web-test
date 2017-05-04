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

    /**
     * @return mixed
     */
    public function getContainerId()
    {
        return $this->container_id;
    }

    /**
     * @param mixed $container_id
     */
    public function setContainerId($container_id)
    {
        $this->container_id = $container_id;
    }

    public function __construct()
    {

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

    public function getProcessInfo($name){
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $command = "ps aux";
        exec("docker exec $this->container_id $command", $output);
        $proc_info = $this->getProcessInfoStringFromCommand($output,$name);

        $result = explode(" ",$proc_info);

        return $this->getMemoryAndTimeUsageOfProcess($result);

    }

    private function getProcessInfoStringFromCommand(array $command_result,$name){
        foreach($command_result as $proc_info) {
            if (strstr($proc_info, $name)) {
                return $proc_info;
            }
        }
        return "";
    }

    private function getMemoryAndTimeUsageOfProcess($processInfo){
        $new_res = [];
        foreach($processInfo as $item){
            if($item != ""){
                $new_res[] = $item;
            }
        }
        $time = $new_res[9];
        $time = explode(":" , $time);
        $time = ["minutes" => $time[0], "seconds" => $time[1]];
        return ["memory" => $new_res[4], "time" => $time];
    }








}