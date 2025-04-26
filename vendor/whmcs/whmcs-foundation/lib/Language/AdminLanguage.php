<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Language;

class AdminLanguage extends AbstractLanguage
{
    protected $globalVariable = "_ADMINLANG";
    public static function getDirectory()
    {
        $adminDirectory = \App::get_admin_folder_name();
        return ROOTDIR . DIRECTORY_SEPARATOR . $adminDirectory . DIRECTORY_SEPARATOR . "lang";
    }
    public static function factory($languageName = self::FALLBACK_LANGUAGE)
    {
        $validLanguageName = self::getValidLanguageName($languageName);
        return static::findOrCreate($validLanguageName);
    }
}

?>