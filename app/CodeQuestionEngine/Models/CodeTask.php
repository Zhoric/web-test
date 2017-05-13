<?php

namespace CodeQuestionEngine;

use Illuminate\Support\Facades\Redis;

/**
 * Модель, инкапсулирующая информацию о задаче с кодом,
 * хранящейся в Redis Cache.
 * @package СodeQuestionEngine
 */

class CodeTask
{
    /**
     * @var string key - формируется как конкатенация префикса, questionId и testCaseNumber
     * Например
     * Вопрос с id = 3 и номером тестовой попытки 2: key: ct-3-2
     */
    public $key;

    public $programId;

    public $processName;

    public $state;

    public $casesCount;

    /**
     * максимальное время выполнения программы в секундах
     */

    public $timeout;

    /**
     * лимит памяти на задачу в килобайтах
     */
    public $memoryLimit;


    public $testCaseNumber;

    /**
     * @var язык программирования
     */
    public $language;


    /**
     * @var string путь к уникальной папке пользователя, где находятся результаты
     * данной задачи
     */
    public $dirPath;

    /**
     * @var айди ответа на вопрос(пустого) он заполнится при проставлении оценки
     */
    public $givenAnsweId;
    /**
     * Префикс ключа задачи с кодом для хранения в кеше
     */
    const  prefix = "ct";


    /**
     * CodeTask constructor.
     * @param $programId
     * @param $givenAnswerId
     * @param $processName
     * @param $language
     * @param $dirPath
     * @param $state
     * @param $key
     * @param $timeout
     * @param $memoryLimit
     * @param string $testCaseNumber
     */
    public function __construct($programId,$givenAnswerId, $language, $dirPath, $processName, $state, $timeout, $memoryLimit, $casesCount = 1, $testCaseNumber = "", $key = "")
    {
        $this->programId   = $programId;
        $this->givenAnswerId = $givenAnswerId;
        $this->timeout = $timeout;
        $this->memoryLimit = $memoryLimit;
        $this->processName   = $processName;
        $this->state = $state;
        $this->testCaseNumber = $testCaseNumber === "" ? 1 : $testCaseNumber;
        $this->language = $language;
        $this->casesCount = $casesCount;
        $this->dirPath = $dirPath;
        $splitted = explode("/", $this->dirPath);
        $name = array_pop($splitted);

        if($key === "") {
            $this->key = self::prefix . '-' . $programId . '-' . $this->testCaseNumber. '-' . $name;
        }
        else {
            $this->key = $key;
        }

    }

    public function store(){

        Redis::hmset($this->key, [
            'key'      => $this->key,
            'programId'    => $this->programId,
            'givenAnswerId' => $this->givenAnswerId,
            'language'     => $this->language,
            'dirPath'      => $this->dirPath,
            'processName'   => $this->processName,
            'state' => $this->state,
            'timeout' => $this->timeout,
            'memoryLimit' => $this->memoryLimit,
            'casesCount'  => $this->casesCount,
            'testCaseNumber' => $this->testCaseNumber,
        ]);
    }


    public static function find($key)
    {
        $stored = Redis::hgetall($key);
        if (!empty($stored)) {
            return new CodeTask(
                  $stored['programId']
                , $stored['givenAnswerId']
                , $stored['language']
                , $stored['dirPath']
                , $stored['processName']
                , $stored['state']
                , $stored['timeout']
                , $stored['memoryLimit']
                , $stored['casesCount']
                , $stored['testCaseNumber']
                , $stored['key']);
        }
        return false;
    }

    public static function getAll()
    {
        $prefix = self::prefix;
        $keys = Redis::keys("$prefix-*");
        $tasks = [];
        foreach ($keys as $key) {
            $stored = Redis::hgetall($key);
            $task = new CodeTask(
                  $stored['programId']
                , $stored['givenAnswerId']
                , $stored['language']
                , $stored['dirPath']
                , $stored['processName']
                , $stored['state']
                , $stored['timeout']
                , $stored['memoryLimit']
                , $stored['casesCount']
                , $stored['testCaseNumber']
                , $stored['key']);

            $tasks[] = $task;
        }
        return $tasks;
    }

    public static function flush(){
        $prefix = self::prefix;
        $keys = Redis::keys("$prefix-*");
        foreach ($keys as $key) {
            Redis::del($key);
        }
    }

    public function delete(){
        Redis::del($this->key);
    }

    public static function deleteByKey($key){
        Redis::del($key);
    }


}