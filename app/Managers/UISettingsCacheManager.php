<?php

namespace Managers;

use Illuminate\Support\Facades\Redis;

/**
 * Клаcc, реализующий механизм хранения и получения настроек
 * пользовательского интерфейса из Redis Cache.
 * Используется, например, для хранения сложных фильтров, чтобы
 * избавить пользователя от необходимости их постоянно заполнять.
 */
class UISettingsCacheManager
{
    /**
     * Клиент Redis Cache.
     * @var Redis
     */
    private $_redisClient;

    /**
     * Время хранения настроек в кэше.
     */
    const cacheExpiration = '+ 1 day';

    /**
     * @return Redis
     */
    private function getRedisClient(){
        if ($this->_redisClient == null){
            $this->_redisClient = app()->make('redis');
        }

        return $this->_redisClient;
    }

    /**
     * Получение значений указанных настроек.
     * @param $userId - Идентификатор текущего пользователя.
     * @param array $settingsKeys - Массив ключей настроек.
     * @return array - Массив из ключей и значений запрошенных настроек.
     */
    public function getValues($userId, array $settingsKeys){
        $values = [];

        for ($i = 0; $i < count($settingsKeys); $i++){
            $value = $this->getRedisClient()->get($settingsKeys[$i].$userId);
            array_push($values, $value);
        }

        return array_combine($settingsKeys, $values);
    }

    /**
     * Установка значений настроек.
     * @param $userId
     * @param $settings - список пар "ключ - значение".
     */
    public function setValues($userId, $settings){
        foreach ($settings as $key => $value){
            $this->getRedisClient()->set($key.$userId, $value);
            $this->getRedisClient()->expireat($key.$userId, strtotime(self::cacheExpiration));
        }
    }
}