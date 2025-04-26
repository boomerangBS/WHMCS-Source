<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Addon\ProjectManagement;

class Container
{
    private $di;
    private static $instance;
    private function __construct()
    {
    }
    public static function getInstance() : \self
    {
        if(is_null(self::$instance)) {
            self::$instance = new self();
            self::$instance->di = new \Illuminate\Container\Container();
        }
        return self::$instance;
    }
    public function make($abstract, array $parameters = [])
    {
        return $this->di->make($abstract, $parameters);
    }
}

?>