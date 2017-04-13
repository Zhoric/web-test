<?php

namespace Helpers;

use DateTime;
use DateTimeZone;
use TestEngine\GlobalTestSettings;

class DateHelper
{
    public static function getCurrentDateTimeString(){
        $now = self::getCurrentDateTime();
        $nowString = $now->format(GlobalTestSettings::dateSerializationFormat);

        return $nowString;
    }

    public static function getCurrentDateTime(){
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(GlobalTestSettings::dateTimeZone));

        return $now;
    }

    public static function addDaysToDate($date,$days){
        $date = strtotime("+".$days." days", strtotime($date));
        return date('Y-m-d', $date);
    }
}