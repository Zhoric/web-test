<?php

namespace Helpers;


use User;

class NameHelper
{
    public static function concatFullName($firstName, $middleName, $lastName){
        $fullName = $lastName;

        $fullName .= ' '.self::getFirstLetter($firstName).'.';
        if (isset($middleName) && strlen($middleName) > 0){
            $fullName .= self::getFirstLetter($middleName).'.';
        }

        return $fullName;
    }

    /**
     * @param $user User
     * @return string
     */
    public static function concatUserFullName($user){
        return self::concatFullName($user->getFirstname(), $user->getPatronymic(), $user->getLastname());
    }

    private static function getFirstLetter($string){
        return mb_substr($string, 0, 1,'UTF8');
    }
}