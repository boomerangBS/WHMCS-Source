<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment;

class DbEngine
{
    const MYSQL_INT_MAX_SIGNED = 2147483647;
    const MYSQL_INT_MAX_UNSIGNED = -1;
    public static function isSupportedByWhmcs($version)
    {
    }
    public static function isSqlStrictMode()
    {
        return \DI::make("db")->isSqlStrictMode();
    }
    public static function getInfo()
    {
        $fullName = \DI::make("db")->getSqlVersionComment();
        if(stripos($fullName, "MariaDB") !== false) {
            $familyName = "MariaDB";
        } elseif(stripos($fullName, "MySQL") !== false) {
            $familyName = "MySQL";
        } else {
            $familyName = "Other";
        }
        $dbVersion = preg_replace("/^([\\d\\.]+)/", "\$1", \DI::make("db")->getSqlVersion());
        return ["family" => $familyName, "fullName" => $fullName, "version" => $dbVersion];
    }
}

?>