<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\File;

class StorageServiceProvider extends \WHMCS\Application\Support\ServiceProvider\AbstractServiceProvider
{
    public function register()
    {
        $this->app->singleton("storage", function () {
            return new Storage();
        });
    }
}

?>