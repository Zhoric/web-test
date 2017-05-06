<?php

namespace Managers;

use Repositories\UnitOfWork;
use Section;
use TestEngine\GlobalTestSettings;

class SettingsManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function get($key){
        $setting = $this->_unitOfWork->settings()->getByKey($key);

        if (!isset($setting)){
            throw new \Exception("Настройка с заданным ключом [$key] отсутствует в базе данных!");
        }
        else {
            return $setting->getValue();
        }
    }

    public function set($key, $value){

        /** @var \TestSetting $setting */
        $setting = $this->_unitOfWork->settings()->getByKey($key);

        if (!isset($setting)){
            throw new \Exception("Настройка с заданным ключом [$key] отсутствует в базе данных!");
        }
        else {
            $setting->setValue($value);
            $this->_unitOfWork->settings()->update($setting);
        }

        $this->_unitOfWork->commit();
    }

    /**
     * Получение всех настроек, конфигурируемых на странице администрирования.
     */
    public function getAll(){
        $settingsKeys = [
            GlobalTestSettings::maxMarkValueKey,
            GlobalTestSettings::complexQuestionPointsKey,
            GlobalTestSettings::firstSemesterMonthKey,
            GlobalTestSettings::secondSemesterMonthKey,
            GlobalTestSettings::questionEndToleranceKey,
            GlobalTestSettings::testEndToleranceKey,
            GlobalTestSettings::testSessionCacheExpirationKey,
            GlobalTestSettings::testSessionTrackingCacheExpirationKey,
        ];
        $settingsValues = [];

        foreach ($settingsKeys as $key){
            array_push($settingsValues, $this->get($key));
        }

        return array_combine($settingsKeys, $settingsValues);
    }

    /**
     * Получение значений по умолчанию всех настроек, конфигурируемых на странице администрирования.
     */
    public function getDefaults(){
        return [
            GlobalTestSettings::maxMarkValueKey => GlobalTestSettings::maxMarkValue,
            GlobalTestSettings::complexQuestionPointsKey => GlobalTestSettings::complexQuestionPoints,
            GlobalTestSettings::firstSemesterMonthKey => GlobalTestSettings::firstSemesterMonth,
            GlobalTestSettings::secondSemesterMonthKey => GlobalTestSettings::secondSemesterMonth,
            GlobalTestSettings::questionEndToleranceKey => GlobalTestSettings::questionEndTolerance,
            GlobalTestSettings::testEndToleranceKey => GlobalTestSettings::testEndTolerance,
            GlobalTestSettings::testSessionCacheExpirationKey => GlobalTestSettings::testSessionCacheExpiration,
            GlobalTestSettings::testSessionTrackingCacheExpirationKey => GlobalTestSettings::testSessionTrackingCacheExpiration,
        ];
    }

    public function setValues($settings){
        foreach ($settings as $key => $value){
            $this->set($key, $value);
        }
    }


}