<?php
namespace CodeQuestionEngine;
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 22.11.16
 * Time: 14:49
 */
class EngineGlobalSettings
{
    /**
     * Название папки, хранящей сборочные файлы, шелл-скрипты
     * Общая папка между виртуальным и реальным хостом
     */
    const CACHE_DIR = "temp_cache";

    /**
     *  Ограничение по памяти для экземпляра виртуальной машины
     *
     */
    const MEMORY_LIMIT = "50M";

    /**
     * Название docker-образа операционной системы
     */
    const IMAGE_NAME = "baseimage-ssh";


    /**
     * имя шелл-скрипта, запускающего код на выполнение.
     * При поддержке многих языков для каждого языка свой шелл-скрипт
     */
    const SHELL_SCRIPT_NAME = "run.sh";
}