<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class Plesk_Config
{
    private static $_settings;
    private static function _init()
    {
        if(!is_null(static::$_settings)) {
            return NULL;
        }
        static::$_settings = json_decode(json_encode(array_merge(static::getDefaults(), static::_getConfigFileSettings())));
    }
    public static function get()
    {
        self::_init();
        return static::$_settings;
    }
    public static function getDefaults()
    {
        return ["account_limit" => 0, "skip_addon_prefix" => false];
    }
    private static function _getConfigFileSettings()
    {
        $filename = dirname(dirname(dirname(__FILE__))) . "/config.ini";
        if(!file_exists($filename)) {
            return [];
        }
        $result = parse_ini_file($filename, true);
        return !$result ? [] : $result;
    }
}

?>