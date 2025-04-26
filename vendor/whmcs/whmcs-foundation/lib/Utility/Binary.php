<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Utility;

// Decoded file for php version 72.
class Binary
{
    public static function strlen($binary_string)
    {
        if(function_exists("mb_strlen")) {
            return mb_strlen($binary_string, "8bit");
        }
        return strlen($binary_string);
    }
    public static function substr($binary_string, $start, $length)
    {
        if(function_exists("mb_substr")) {
            return mb_substr($binary_string, $start, $length, "8bit");
        }
        return substr($binary_string, $start, $length);
    }
}

?>