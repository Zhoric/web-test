<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use CodeQuestionEngine\EngineGlobalSettings;
use CodeQuestionEngine\DockerInstance;
class TestJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    private $dockerInstance;
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
    public function handle()
    {

        $container_id =  "41b78207d68b";


        $command_pattern = "sh /opt/temp_cache/code/run.sh";

        $descriptorspec = array(
            0 => array('pipe', 'r'),
        );

        $start_time = microtime(true);

        $process = proc_open("docker exec $container_id $command_pattern",
            $descriptorspec,$pipes);

        return;

        $current_time = microtime(true);


        while($current_time - $start_time < 1){
            $metainfo = proc_get_status($process);



            if($metainfo["running"] === false){

                return;

            }
            sleep(0.2);

            $current_time = microtime(true);
        }



         $command_pattern = "docker stop $container_id";

         exec($command_pattern,$output);





    }


    private function oldHandle(DockerInstance $dockerInstance){
        $this->dockerInstance = $dockerInstance;
        $app_path = app_path();
        $cache_dir = EngineGlobalSettings::CACHE_DIR;

        $dirPath = "$app_path/$cache_dir/code";
        file_get_contents("$dirPath/test.c");

        $this->dockerInstance->runAsync("sh /opt/temp_cache/code/run.sh");
    }
}
