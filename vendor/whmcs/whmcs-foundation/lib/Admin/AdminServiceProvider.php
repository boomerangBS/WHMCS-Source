<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin;

class AdminServiceProvider extends \WHMCS\Application\Support\ServiceProvider\AbstractServiceProvider
{
    public function register()
    {
        if(!defined("ADMINAREA")) {
            define("ADMINAREA", true);
        }
        if(!function_exists("checkPermission")) {
            gracefulCoreRequiredFileInclude("/includes/adminfunctions.php");
        }
    }
    public static function getAdminRouteBase()
    {
        $adminDirectoryName = \App::get_admin_folder_name();
        if(substr($adminDirectoryName, 0, 1) != "/") {
            $adminDirectoryName = "/" . $adminDirectoryName;
        }
        if(substr($adminDirectoryName, -1) == "/") {
            $adminDirectoryName = substr($adminDirectoryName, 0, -1);
        }
        return $adminDirectoryName;
    }
    public static function hasDefaultAdminDirectory()
    {
        return is_dir(ROOTDIR . DIRECTORY_SEPARATOR . \WHMCS\Config\Application::DEFAULT_ADMIN_FOLDER);
    }
    public static function hasConfiguredCustomAdminPath()
    {
        $adminPath = \DI::make("config")->customadminpath;
        if(!$adminPath) {
            return false;
        }
        return $adminPath != \WHMCS\Config\Application::DEFAULT_ADMIN_FOLDER;
    }
    public static function hasCustomAdminPathCollisionWithRoutes()
    {
        $customAdminPath = \DI::make("config")->customadminpath;
        if(!$customAdminPath) {
            return false;
        }
        $clientRouteSpace = (new \WHMCS\ClientArea\ClientAreaServiceProvider(\DI::make("di")))->getFirstLevelDirectoryNames();
        return in_array($customAdminPath, $clientRouteSpace);
    }
}

?>