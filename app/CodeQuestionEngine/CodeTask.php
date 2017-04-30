<?php

namespace CodeQuestionEngine;

use Illuminate\Support\Facades\Redis;

/**
 * Класс, инкапсулирующий информацию о задаче с кодом,
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

    public $questionId;

    public $processName;

    public $state;

    public $testCaseNumber;

    /**
     * Префикс ключа задачи с кодом для хранения в кеше
     */
    const  prefix = "ct";


    /**
     * CodeTask constructor.
     * @param $questionId
     * @param $processName
     * @param $state
     * @param $key
     * @param string $testCaseNumber
     */
    public function __construct($questionId, $processName, $state, $testCaseNumber = "", $key = "")
    {
        $this->questionId   = $questionId;
        $this->processName   = $processName;
        $this->state = $state;
        $this->testCaseNumber = empty($testCaseNumber) ? 1 : $testCaseNumber;

        if(empty($key)) {
            $this->key = self::prefix . '-' . $questionId . '-' . $testCaseNumber;
        }
        else {
            $this->key = $key;
        }
    }

    public function store(){

        Redis::hmset($this->key, [
            'key'      => $this->key,
            'questionId'    => $this->questionId,
            'processName'   => $this->processName,
            'state' => $this->state,
            'testCaseNumber' => $this->testCaseNumber,
        ]);
    }


    public static function find($key)
    {
        $stored = Redis::hgetall($key);
        if (!empty($stored)) {
            return new CodeTask(
                  $stored['questionId']
                , $stored['processName']
                , $stored['state']
                , $stored['testCaseNumber']
                , $stored['key']);
        }
        return false;
    }

    public static function getAll()
    {
        $prefix = self::prefix;
        $keys = Redis::keys("$prefix:*");
        $clients = [];
        foreach ($keys as $key) {
            $stored = Redis::hgetall($key);
            $task = new CodeTask(
                $stored['questionId']
                , $stored['processName']
                , $stored['state']
                , $stored['testCaseNumber']
                , $stored['key']);

            $tasks[] = $task;
        }
        return $clients;
    }

    public function delete(){
        Redis::del($this->key);
    }

    public static function deleteByKey($key){
        Redis::del($key);
    }


}