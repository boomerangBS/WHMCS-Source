<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\Transip;

class Autoload
{
    public static function autoload($className) : void
    {
        $namespacePath = str_replace("Transip\\Api\\Library", "vendor" . DIRECTORY_SEPARATOR . "Transip", $className);
        $namespacePath = str_replace("\\", DIRECTORY_SEPARATOR, $namespacePath);
        $filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $namespacePath . ".php";
        if(!file_exists($filePath)) {
            return NULL;
        }
        require_once $filePath;
    }
}

?>