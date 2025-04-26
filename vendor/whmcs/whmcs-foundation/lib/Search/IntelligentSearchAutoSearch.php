<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Search;

class IntelligentSearchAutoSearch
{
    const SESSION_STORAGE_NAME = "intelligentSearchAutoSearch";
    public static function isEnabled()
    {
        if(\WHMCS\Session::exists(self::SESSION_STORAGE_NAME)) {
            return (bool) \WHMCS\Session::get(self::SESSION_STORAGE_NAME);
        }
        return true;
    }
    public static function setStatus($enabled)
    {
        \WHMCS\Session::set(self::SESSION_STORAGE_NAME, (bool) $enabled);
    }
}

?>