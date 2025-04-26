<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
class Terminus
{
    private static $instance;
    protected static function setInstance(Terminus $terminus)
    {
        self::$instance = $terminus;
        return $terminus;
    }
    protected static function destroyInstance()
    {
        self::$instance = NULL;
    }
    public static function getInstance()
    {
        if(is_null(self::$instance)) {
            self::setInstance(new Terminus());
        }
        return self::$instance;
    }
    public function doExit($status = 0)
    {
        $status = (int) $status;
        exit($status);
    }
    public function doDie($msg = "")
    {
        if(!headers_sent()) {
            header("HTTP/1.1 500 Internal Server Error");
        }
        if(is_string($msg)) {
            exit($msg);
        }
        exit;
    }
}

?>