<?php

namespace App\Console\Commands;

use CodeQuestionEngine\DockerManager;
use CodeQuestionEngine\EngineGlobalSettings;
use Illuminate\Console\Command;
use CodeQuestionEngine\CodeTask;
use CodeTaskStatus;
use Queue;

class OperateTaskStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:operate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command
     * @param DockerManager $dockerManager
     * @return mixed
     */
    public function handle(DockerManager $dockerManager)
    {
       while(true){

           $tasks = CodeTask::getAll();

           foreach($tasks as $task){

               switch($task->state){

                   case CodeTaskStatus::Running:
                   {
                        $dockerManager->setLanguage($task->language);
                        $dockerInstance = $dockerManager->getOrCreateInstance();
                        $processInfo = $dockerInstance->getProcessInfo($task->processName);
                        if(!empty($processInfo)) {
                            if (($processInfo["memory"] - EngineGlobalSettings::STANDART_MEMORY_USAGE)
                                > $task->memoryLimit
                            ) {
                                $task->state = CodeTaskStatus::MemoryOverflow;
                                $dockerInstance->killProcess($task->processName);
                                //todo: поставить двойку
                            }
                            if($processInfo["time"]["seconds"] > $task->timeLimit){
                                $task->state = CodeTaskStatus::Timeout;
                                $dockerInstance->killProcess($task->processName);
                                //todo: поставить двойку
                            }
                        }
                        else{
                            $task->state = CodeTaskStatus::QueuedToCheck;
                            //todo: отправить на проверку
                        }
                        $task->store();
                   }break;
               }

           }
        sleep(1);
       }
    }
}
