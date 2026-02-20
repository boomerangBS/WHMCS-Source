<?php

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