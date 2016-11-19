<?php

namespace Managers;

use Repositories\UnitOfWork;
use Section;

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
            throw new \Exception('Настройки с заданным ключом не существует!');
        }
        else {
            return $setting->getValue();
        }
    }

    public function set($key, $value){
        $setting = $this->_unitOfWork->settings()->getByKey($key);

        if (!isset($setting)){
            $newSetting = new \TestSetting();
            $newSetting->setKey($key);
            $newSetting->setValue($value);

            $this->_unitOfWork->settings()->create($setting);
        }
        else {
            $this->_unitOfWork->settings()->update($setting);
        }

        $this->_unitOfWork->commit();
    }

}