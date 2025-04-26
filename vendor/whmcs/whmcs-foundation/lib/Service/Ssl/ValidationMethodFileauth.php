<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service\Ssl;

class ValidationMethodFileauth extends ValidationMethod
{
    public $name;
    public $path;
    public $contents;
    public function methodNameConstant()
    {
        return \WHMCS\Service\Ssl::DOMAIN_VALIDATION_FILE;
    }
    public function friendlyName()
    {
        return "HTTP File";
    }
    public function translationKey(\WHMCS\Language\AbstractLanguage $language) : \WHMCS\Language\AbstractLanguage
    {
        if($language instanceof \WHMCS\Language\AdminLanguage) {
            return "wizard.ssl.fileMethod";
        }
        return "ssl.fileMethod";
    }
    public function populate($values) : ValidationMethod
    {
        return $this->populateFromClassProperties($values);
    }
    public function defaults() : ValidationMethod
    {
        return $this;
    }
    public function filePath()
    {
        return sprintf("%s/%s", $this->path, $this->name);
    }
    public function toArray() : array
    {
        return ["fileAuthPath" => $this->path, "fileAuthFilename" => $this->name, "fileAuthContents" => $this->contents];
    }
}

?>