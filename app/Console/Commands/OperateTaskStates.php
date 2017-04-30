<?php

namespace App\Console\Commands;

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
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       while(true){

           $tasks = CodeTask::getAll();

           foreach($tasks as $task){

               switch($task->state){

                   case CodeTaskStatus::Running:
                   {

                   }break;
               }

           }
        sleep(1);
       }
    }
}
