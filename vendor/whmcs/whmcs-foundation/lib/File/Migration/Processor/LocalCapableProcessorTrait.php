<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\File\Migration\Processor;

trait LocalCapableProcessorTrait
{
    private $targetDirMode;
    public function validateLocalPath($localDir)
    {
        if(is_dir($localDir) && is_writable($localDir)) {
            $this->targetDirMode = stat($localDir)["mode"];
        } else {
            throw new \WHMCS\Exception\Storage\AssetMigrationException(sprintf("%s directory does not exist or is not writable", $localDir));
        }
    }
    public function createDirectoriesForFile($filePath)
    {
        if(is_null($this->targetDirMode)) {
            throw new \WHMCS\Exception\Storage\AssetMigrationException("Invalid target dir mode - must have run validateLocalPath() first");
        }
        $dirPath = dirname($filePath);
        if(is_dir($dirPath)) {
            if(!is_writable($dirPath)) {
                throw new \WHMCS\Exception\Storage\AssetMigrationException(sprintf("%s directory exists but is not writable", $dirPath));
            }
        } elseif(!mkdir($dirPath, $this->targetDirMode, true)) {
            throw new \WHMCS\Exception\Storage\AssetMigrationException(sprintf("Cannot create directory: %s", $dirPath));
        }
    }
}

?>