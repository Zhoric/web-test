<?php

namespace App\Jobs;

use CodeQuestionEngine\CodeTask;
use CodeQuestionEngine\DockerManager;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use CodeQuestionEngine\DockerInstance;


class RunProgramJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var \Language
     */
    protected $lang;



    public function __construct($lang,$command)
    {
        $this->lang = $lang;
        $this->command = $command;
    }


    public function handle(DockerManager $dockerManager)
    {

        $dockerManager->setLanguage($this->lang);
        $dockerInstance = $dockerManager->getOrCreateInstance();
        $dockerInstance->runAsync($this->command);


        echo "finished";
        return;
    }


}
