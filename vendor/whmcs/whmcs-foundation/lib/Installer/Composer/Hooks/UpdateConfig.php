<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Installer\Composer\Hooks;

class UpdateConfig extends \WHMCS\Config\Application
{
    protected function checkConfigLoaded()
    {
        if(!$this->isConfigFileLoaded()) {
            throw new \WHMCS\Exception("Configuration file has not been loaded yet");
        }
    }
    public function getCustomAdminPath()
    {
        $this->checkConfigLoaded();
        return $this->customadminpath;
    }
    public function getCustomAttachmentsDir()
    {
        $this->checkConfigLoaded();
        return $this->attachments_dir;
    }
    public function getCustomCompiledTemplatesDir()
    {
        $this->checkConfigLoaded();
        return $this->templates_compiledir;
    }
    public function getCustomDownloadsDir()
    {
        $this->checkConfigLoaded();
        return $this->downloads_dir;
    }
    public function getCustomCronsDir()
    {
        $this->checkConfigLoaded();
        return $this->crons_dir;
    }
}

?>