<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Filter;

class Input
{
    public static function url($url)
    {
        if(function_exists("filter_var")) {
            return filter_var($url, FILTER_VALIDATE_URL);
        }
        $streamPattern = "/^[a-zA-Z0-9]+\\s?:\\s?\\//";
        if(preg_match($streamPattern, $url)) {
            return $url;
        }
        return false;
    }
}

?>