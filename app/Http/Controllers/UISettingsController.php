<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Managers\UISettingsCacheManager;


class UISettingsController extends Controller{

    private $_uiSettingsManager;

    public function __construct(UISettingsCacheManager $uiSettingsManager)
    {
        $this->_uiSettingsManager = $uiSettingsManager;
    }

    /**
     * Установка настроек. Пример тела запроса:
     * {"settings": {"hello":"hello world!", "test": 666}}
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function setSettings(Request $request){
        try{
            $settings = $request->json('settings');
            $userId = $this->tryGetUserId();
            $this->_uiSettingsManager->setValues($userId, $settings);

            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /**
     * Получение настроек. Пример тела запроса:
     * {"settings":["hello","test"]}
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function getSettings(Request $request){
        try{
            $settingKeys = $request->json()->get("settings");
            $userId = $this->tryGetUserId();
            $settings = $this->_uiSettingsManager->getValues($userId, $settingKeys);

            return $this->successJSONResponse($settings);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    private function tryGetUserId(){
        $currentUser = Auth::user();

        if (!isset($currentUser)){
            throw new \Exception('Для данного действия необходима авторизация!');
        }

        return $currentUser->getId();
    }


}