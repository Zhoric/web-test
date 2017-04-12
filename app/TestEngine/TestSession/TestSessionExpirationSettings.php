<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 12.04.17
 * Time: 20:08
 */

namespace TestEngine;
use Managers\SettingsManager;

/**
 * Синглтон, хранящий время, в течение которого необходимо хранить сессии тестирования и записи об их существовании,
 * которые используются для мониторинга.
 * @package TestEngine
 */
class TestSessionExpirationSettings
{
    private $_settings = array();

    /**
     * @var SettingsManager
     */
    private $_settingsManager;

    /**
     * Время, в течение которого хранится сессия тестирования.
     */
    private $_sessionExpirationTime;

    /**
     * Время, в течение которого хранится запись о существовании сессии тестирования (используется для мониторинга).
     */
    private $_sessionMonitoringExpirationTime;

    private static $_instance = null;

    private function __construct() {
    }

    protected function __clone() {
    }

    static public function getInstance() {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_settingsManager = app()->make(SettingsManager::class);

            self::$_instance->_sessionExpirationTime =
                self::$_instance->_settingsManager
                    ->get(GlobalTestSettings::testSessionCacheExpirationKey);

            self::$_instance->_sessionMonitoringExpirationTime =
                self::$_instance->_settingsManager
                    ->get(GlobalTestSettings::testSessionTrackingCacheExpirationKey);
        }
        return self::$_instance;
    }

    public function getTestSessionExpirationTime() {
        if (!isset($this->_sessionExpirationTime) || empty($this->_sessionExpirationTime)){
            throw new \Exception('Не удалось получить настройку времени хранения сессии тестирования.');
        }
        return $this->_sessionExpirationTime;
    }

    public function getTestSessionMonitoringExpirationTime() {
        if (!isset($this->_sessionMonitoringExpirationTime) || empty($this->_sessionMonitoringExpirationTime)){
            throw new \Exception('Не удалось получить настройку времени хранения записи в кэше о существовании сессии тестирования [для мониторинга].');
        }
        return $this->_sessionMonitoringExpirationTime;
    }
}