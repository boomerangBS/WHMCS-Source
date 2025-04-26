<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv;

// Decoded file for php version 72.
class SystemConfiguration
{
    protected $application;
    protected static $singleton;
    public static function singleton(\WHMCS\Application $app)
    {
        if(!is_null(self::$singleton)) {
            return self::$singleton;
        }
        self::$singleton = new self($app);
        return self::$singleton;
    }
    public function __construct(\WHMCS\Application $app)
    {
        $this->application = $app;
    }
    public function app() : \WHMCS\Application
    {
        return $this->application;
    }
}

?>