<?php

namespace App\Http\Controllers;

use Exception;
use Group;
use Illuminate\Http\Request;
use Managers\GroupManager;
use Managers\SettingsManager;
use User;

class SettingsController extends Controller
{
    private $_settingsManager;

    public function __construct(SettingsManager $settingsManager)
    {
        $this->_settingsManager = $settingsManager;
    }

    /***
     * Установка значения настройки по ключу.
     */
    public function setValue(Request $response){
        try{
            $key = $response->json('key');
            $value = $response->json('value');
            $this->_settingsManager->set($key, $value);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /***
     * Получение значения настройки.
     */
    public function getValue($key){
        try{
            $value = $this->_settingsManager->get($key);
            return $this->successJSONResponse(['value' => $value]);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}
