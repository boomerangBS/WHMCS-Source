<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Filter;

class Json
{
    private static $maxLength = 65536;
    public static function safeDecode($content, $assoc = false, $depth = 512, $options = 0)
    {
        if(self::$maxLength < strlen($content)) {
            if(defined("JSON_THROW_ON_ERROR") && class_exists("\\JsonException") && $options & JSON_THROW_ON_ERROR) {
                throw new \JsonException("JSON content too long");
            }
            return NULL;
        }
        return json_decode($content, $assoc, $depth, $options);
    }
}

?>