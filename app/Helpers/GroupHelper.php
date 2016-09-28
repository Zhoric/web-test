<?php

namespace Helpers;

const fullTimeEnding = 'о';
const extramuralEnding = 'з';

class GroupHelper
{
    public static function GenerateGroupName($prefix, $year, $number, $isFullTime)
    {
        $groupName = $prefix.'-'.$year.$number;
        if ($isFullTime){
            return $groupName.fullTimeEnding;
        } else{
            return $groupName.extramuralEnding;
        }
    }
}