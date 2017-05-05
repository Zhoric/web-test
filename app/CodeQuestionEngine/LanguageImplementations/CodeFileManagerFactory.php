<?php


namespace CodeQuestionEngine;
use CodeFileManager;
use Exception;


class CodeFileManagerFactory
{
    /**
     * Получение менеджера вопросов с программным кодом для подсчёта оценки за ответ на
     * вопрос с кодом.
     * @param $lang - язык программирования, который используется при запуске программы на выполнение
     * @return CodeFileManager
     * @throws Exception
     */
    public static function getCodeFileManager($lang){

        $fileManager = app()->make(CodeFileManager::class);

        if(isset($fileManager)) {
            $fileManager->setCacheDirName(EngineGlobalSettings::CACHE_DIR);
            $fileManager->setBaseShellScriptName(EngineGlobalSettings::SHELL_SCRIPT_NAME_ARRAY[$lang]);
            $fileManager->setCodeFileExtension(EngineGlobalSettings::CODE_FILE_EXTENSIONS_ARRAY[$lang]);
            $fileManager->setExecuteFileName(EngineGlobalSettings::EXECUTE_FILE_NAME[$lang]);

        }
        else throw new Exception("Не реализован класс файлового менеджера для нового языка программирования");

        return $fileManager;

    }
}