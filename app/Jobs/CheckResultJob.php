<?php

namespace App\Jobs;

use CodeQuestionEngine\CodeFileManagerFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckResultJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var \Language
     */
    protected $language;

    /**
     * @var  \CodeFileManager
     */
    protected $fileManager;

    /**
     * @var array
     */
    protected $codeTasks;

    /**
     * Create a new job instance.
     *
     */

    public function __construct($lang, array $codeTasks)
    {
        $this->codeTasks = $codeTasks;
        $this->language = $lang;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       $this->fileManager =  CodeFileManagerFactory::getCodeFileManager($this->language);
       $mark = $this->fileManager->calculateMark($this->codeTasks);
       foreach($this->codeTasks as $codeTask){

           $codeTask->delete();
       }
    }
}
