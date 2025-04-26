<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version700alpha1 extends IncrementalVersion
{
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $config = \DI::make("config");
        $this->filesToRemove[] = ROOTDIR . DIRECTORY_SEPARATOR . ($config["customadminpath"] ?: "admin") . DIRECTORY_SEPARATOR . "browser.php";
    }
}

?>