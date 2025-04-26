<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View;

class ViewServiceProvider extends \WHMCS\Application\Support\ServiceProvider\AbstractServiceProvider
{
    public function register()
    {
        $this->app->singleton("asset", function () {
            return new Asset(\WHMCS\Utility\Environment\WebHelper::getBaseUrl(ROOTDIR, $_SERVER["SCRIPT_NAME"]));
        });
        $this->app->bind("View\\Engine\\Php\\Admin", function () {
            return (new Engine\Php\Admin())->loadExtension(new PlatesExtension\SelectOptions());
        });
        $this->app->bind("View\\Engine\\Smarty\\Admin", function () {
            return new Engine\Smarty\Admin();
        });
    }
}

?>