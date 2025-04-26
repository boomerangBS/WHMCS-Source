<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version860release1 extends IncrementalVersion
{
    protected $updateActions = ["removeUnusedLegacyModules"];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove[] = ROOTDIR . "/assets/js/tiny_mce";
        $this->filesToRemove[] = ROOTDIR . "/admin/whmimport.php";
        $this->filesToRemove[] = ROOTDIR . "/resources/views/admin/client/products";
        $this->filesToRemove[] = ROOTDIR . "/modules/social";
    }
    public function removeUnusedLegacyModules()
    {
        (new \WHMCS\Module\LegacyModuleCleanup())->removeModulesIfInstalledAndUnused(["gateways" => ["paypalexpress"]]);
        return $this;
    }
}

?>