<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version720rc1 extends IncrementalVersion
{
    protected $updateActions = ["addSystemURLIfNotDefined"];
    protected function addSystemURLIfNotDefined()
    {
        if(\WHMCS\Config\Setting::getValue("SystemURL")) {
            return $this;
        }
        if(!isset($_SERVER["SERVER_NAME"]) && !isset($_SERVER["HTTP_HOST"])) {
            return $this;
        }
        $prefix = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] ? "https" : "http";
        $url = $prefix . "://" . $_SERVER["SERVER_NAME"] . preg_replace("#/[^/]*\\.php\$#simU", "/", $_SERVER["PHP_SELF"]);
        $url = str_replace("/install/", "/", $url);
        $url = str_replace("/install2/", "/", $url);
        \WHMCS\Config\Setting::setValue("SystemURL", $url);
        $updater = new Version720alpha1($this->version);
        $updater->detectAndSetUriPathMode();
        return $this;
    }
}

?>