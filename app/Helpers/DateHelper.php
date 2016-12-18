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
}