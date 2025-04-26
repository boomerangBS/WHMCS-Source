<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Application\Support\ServiceProvider;

abstract class AbstractServiceProvider
{
    protected $app;
    public function __construct(\WHMCS\Container $app)
    {
        $this->app = $app;
    }
    public abstract function register();
}

?>