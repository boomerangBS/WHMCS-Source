<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Installer;

class DatabaseContent
{
    protected $schemaDirectory = "";
    public function __construct($schemaDirectory = NULL)
    {
        if(!$schemaDirectory) {
            $schemaDirectory = $this->getDefaultSchemaDirectory();
        }
        $this->setSchemaDirectory($schemaDirectory);
    }
    public function getDefaultSchemaDirectory()
    {
        return ROOTDIR . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "install" . DIRECTORY_SEPARATOR;
    }
    public function getSchemaDirectory()
    {
        return $this->schemaDirectory;
    }
    public function setSchemaDirectory($schemaDirectory)
    {
        $this->schemaDirectory = $schemaDirectory;
        return $this;
    }
    public function getDatabaseSeedContent()
    {
        $installSchema = $installData = "";
        $resourcesPath = $this->getSchemaDirectory();
        foreach (glob($resourcesPath . "*.schema.sql") as $filename) {
            $installSchema .= file_get_contents($filename);
        }
        foreach (glob($resourcesPath . "*.data.sql") as $filename) {
            $installData .= file_get_contents($filename);
        }
        return $installSchema . "\n" . $installData;
    }
}

?>