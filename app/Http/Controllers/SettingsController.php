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
     * Получение значения настройки.
     */
    public function get($key){
        try{
            $value = $this->_settingsManager->get($key);
            return $this->successJSONResponse(['value' => $value]);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /**
     * Установка настроек. Пример тела запроса:
     * {"settings": {"maxMarkValue":"100","firstSemesterMounth":"8","secondSemesterMounth":"1",
     * "questionEndTolerance":"5","testEndTolerance":"30","cacheExpiration":"+ 1 day",
     * "testSessionTrackingCacheExpiration":"+ 5 hours"}}
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function set(Request $request){
        try{
            $settings = $request->json('settings');
            $this->_settingsManager->setValues($settings);

            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAll(Request $request){
        try{
            $settings = $this->_settingsManager->getAll();

            return $this->successJSONResponse($settings);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getDefaults(){
        try{
            $settings = $this->_settingsManager->getDefaults();

            return $this->successJSONResponse($settings);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}
