<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Installer\Composer\Hooks;

class ComposerInstallerHook
{
    const DEFAULT_INSTALLATION_DIR = ".";
    public static function postInstallCmd(\Composer\Script\Event $event)
    {
        self::dispatch($event);
    }
    public static function postUpdateCmd(\Composer\Script\Event $event)
    {
        self::dispatch($event);
    }
    protected static function dispatch(\Composer\Script\Event $event)
    {
        $installer = new ComposerInstaller($event, realpath(self::DEFAULT_INSTALLATION_DIR));
        $installer->run();
    }
}

?>