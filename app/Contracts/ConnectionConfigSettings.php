<?php

class ConnectionConfigSettings
{


    /**
     * @var string адрес сервиса обработки вопросов с кодом
     */
    public static $BASE_URL = "www.code-question.ru";


    /**
     * @var string URL Для запуска вопросов с кодом
     */
    public static $RUN_QUESTION_PROGRAM_URL = "/api/program/runQuestionProgram";

    /**
     * @var string URL Для запуска программ(используется админом при составлении вопросов)
     */
    public static $RUN_PROGRAM_URL = "/api/program/runProgram";

    /**
     * @var array Белый список IP  для пользования внешними модулями
     */
    public static $WHITE_LIST = ["127.0.0.1","192.168.0.100"];

}