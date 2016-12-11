<?php

namespace Helpers;

use DateTime;
use DateTimeZone;

class DateHelper
{
    private static $dateFormat = 'Y-m-d H:i:s';

    public static function getCurrentDateTimeString(){

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Moscow'));
        $nowString = $now->format(self::$dateFormat);

        return $nowString;
    }
}