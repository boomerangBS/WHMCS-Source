<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version860alpha1 extends IncrementalVersion
{
    protected $updateActions = ["removeUnusedLegacyModules"];
    public function getUnusedLegacyModules()
    {
        return ["gateways" => ["chronopay", "eonlinedata"]];
    }
    public function removeUnusedLegacyModules()
    {
        (new \WHMCS\Module\LegacyModuleCleanup())->removeModulesIfInstalledAndUnused($this->getUnusedLegacyModules());
        return $this;
    }
}

?>