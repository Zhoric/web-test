<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 03.11.16
 * Time: 1:07
 */

namespace Helpers;
use Exception;

/**
 * Вспомогательный класс для работы с файлами
 */
class FileHelper
{
    private static $savePath = 'images/questions/';
    private static $fileNameLength = 30;

    public static function save($fileContent, $fileType){
        $fileName = self::getRandomString(self::$fileNameLength);

        $extension = self::getFileExtension($fileType);

        $filePath = self::$savePath.$fileName.$extension;
        $file = fopen($filePath, "a");

        fwrite($file, base64_decode($fileContent));
        fclose($file);

        return $filePath;
    }

    private static function getFileExtension($fileTypeString){
        if (strpos($fileTypeString, 'jpg') !== false || (strpos($fileTypeString, 'jpeg') !== false)){
            return '.jpg';
        } else if (strpos($fileTypeString, 'png') !== false){
            return '.png';
        } else if (strpos($fileTypeString, 'gif') !== false){
            return '.gif';
        } else {
            throw new Exception("Недопустимый формат файла!");
        }
    }

    private static function getRandomString($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }
}