<?php

namespace App\Http\Controllers;

use Group;
use Illuminate\Http\Request;

use Managers\GroupManager;
use Managers\SettingsManager;
use User;

class SettingsController extends Controller
{
    private $_settingsManager;

    /***
     * Получение значения настройки по ключу.
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->_settingsManager = $settingsManager;
    }

    /***
     * Установка значения настройки.
     */
    public function getValue($key){
        try{
            $value = $this->_settingsManager->get($key);
            return json_encode(['value' => $value]);

        } catch (\Exception $exception){
            return json_encode(['message' => $exception->getMessage()]);
        }
    }
}
