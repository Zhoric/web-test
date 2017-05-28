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
    private static $fileNameLength = 15;

    /**
     * Сохранение файла. Если имя не указано, оно будет сгенерировано.
     * @param $fileContent
     * @param $fileType
     * @param null $filePath
     * @return string
     * @internal param null $savePath
     */
    public static function save($fileContent, $fileType, $filePath = null){

        if (!isset($filePath)){
            do {
                $extension = self::tryGetFileExtension($fileType);
                $fileName = self::getRandomString(self::$fileNameLength);
                $filePath = self::$savePath.$fileName.$extension;
            } while (file_exists($filePath));
        }


        $file = fopen($filePath, "a");
        fwrite($file, base64_decode($fileContent));
        fclose($file);

        return $filePath;
    }

    public static function delete($filePath){
        if (file_exists($filePath)){
            unlink($filePath);
        }
    }

    private static function tryGetFileExtension($fileTypeString){
        if (strpos($fileTypeString, 'jpg') !== false || (strpos($fileTypeString, 'jpeg') !== false)){
            return '.jpg';
        } else if (strpos($fileTypeString, 'png') !== false){
            return '.png';
        } else if (strpos($fileTypeString, 'gif') !== false){
            return '.gif';
        } else if (strpos($fileTypeString, 'bmp') !== false){
            return '.bmp';
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