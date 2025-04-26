<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version742release1 extends IncrementalVersion
{
    protected $updateActions = ["removeComposerInstallUpdateHooks"];
    protected function removeComposerInstallUpdateHooks()
    {
        $directoryToClean = ROOTDIR . str_replace("/", DIRECTORY_SEPARATOR, "/vendor/whmcs/whmcs-foundation/lib/Installer/Composer/Hooks");
        if(is_dir($directoryToClean)) {
            \WHMCS\Utility\File::recursiveDelete($directoryToClean);
        }
    }
}

?>