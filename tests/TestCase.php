<?php

use Illuminate\Redis\Database;

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    protected $redisClient;

    /**
     * Получение клиента для доступа к хранилищу Redis Cache.
     * @return Database
     */
    protected function getRedisClient(){
        if ($this->redisClient == null){
            $this->redisClient = app()->make(Database::class);
        }
        return $this->redisClient;
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function writeConsoleMessage($message, $color = 'white', $newLines = 0){

        switch ($color) {
            case 'red': {
                $colorEscapeSequence = "\033[31m";
                break;
            }
            case 'green': {
                $colorEscapeSequence = "\033[32m";
                break;
            }
            case 'grey' : {
                $colorEscapeSequence = "\033[0;37m";
                break;
            }
            case 'blue' : {
                $colorEscapeSequence = "\033[1;34m";
                break;
            }
            case 'cyan' : {
                $colorEscapeSequence = "\033[0;36m";
                break;
            }
            default:{
                $colorEscapeSequence = "\033[1;37m";
            }
        }

        fwrite(STDOUT, $colorEscapeSequence.'  '.$message.$colorEscapeSequence);
        for ($i = 0; $i < $newLines; $i++){
            fwrite(STDOUT, PHP_EOL);
        }
    }

    protected function writeOk($newLines = 0){
        $newLinesTotalCount = 1 + $newLines;
        $this->writeConsoleMessage('OK.', 'green', $newLinesTotalCount);
    }

    protected function writeNewLine(){
        fwrite(STDOUT, PHP_EOL);
    }

    protected function writeApiCall($url){
        $this->writeConsoleMessage('URL: '.$url, 'cyan', 0);
    }
}
