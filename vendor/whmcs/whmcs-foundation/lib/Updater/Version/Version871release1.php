<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version871release1 extends IncrementalVersion
{
    protected $updateActions = [];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove[] = implode(DIRECTORY_SEPARATOR, [ROOTDIR, "modules", "servers", "licensing", "clientarea.tpl"]);
        $this->filesToRemove[] = implode(DIRECTORY_SEPARATOR, [ROOTDIR, "modules", "servers", "licensing", "clientareaproductdetails.tpl"]);
        $this->filesToRemove[] = implode(DIRECTORY_SEPARATOR, [ROOTDIR, "modules", "servers", "licensing", "clientareaproductdetails_portal.tpl"]);
    }
}

?>