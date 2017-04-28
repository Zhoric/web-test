<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use CodeQuestionEngine\EngineGlobalSettings;
use CodeQuestionEngine\DockerEngine;
class TestJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    private $dockerEngine;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DockerEngine $dockerEngine)
    {


        $this->dockerEngine = $dockerEngine;
        $app_path = app_path();
        $cache_dir = EngineGlobalSettings::CACHE_DIR;

        $dirPath = "$app_path/$cache_dir/code";
        file_get_contents("$dirPath/test.c");

        $this->dockerEngine->runAsync("sh /opt/temp_cache/code/run.sh");

    }
}
