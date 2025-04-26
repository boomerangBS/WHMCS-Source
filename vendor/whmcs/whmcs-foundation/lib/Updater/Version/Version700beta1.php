<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version700beta1 extends IncrementalVersion
{
    protected $updateActions = ["removeLegacyClassLocations"];
    public function removeLegacyClassLocations()
    {
        $legacyClassesDir = ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR;
        $dirsToRemove = [$legacyClassesDir . "WHMCS", $legacyClassesDir . "phlyLabs"];
        foreach ($dirsToRemove as $dir) {
            if(is_dir($dir)) {
                try {
                    \WHMCS\Utility\File::recursiveDelete($dir);
                } catch (\Exception $e) {
                }
            }
        }
        return $this;
    }
}

?>