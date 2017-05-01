<?php


namespace CodeQuestionEngine;
use CodeFileManagerBase;
use Language;
use Exception;


class CodeFileManagerFactory
{
    /**
     * Получение менеджера вопросов с программным кодом для подсчёта оценки за ответ на
     * вопрос с кодом.
     * @param $lang - язык программирования, который используется при запуске программы на выполнение
     * @return CodeFileManagerBase
     * @throws Exception
     */
    public static function getCodeFileManager($lang){

        switch($lang){
            case Language::C: {
                $fileManager = app()->make(CCodeFileManager::class);
            }break;
        }

        if(isset($fileManager)) {
            $fileManager->setCacheDirName(EngineGlobalSettings::CACHE_DIR);
            $fileManager->setScriptName(EngineGlobalSettings::SHELL_SCRIPT_NAME_ARRAY[$lang]);
        }
        else throw new Exception("Не реализован класс файлового менеджера для нового языка программирования");

        return $fileManager;

    }
}