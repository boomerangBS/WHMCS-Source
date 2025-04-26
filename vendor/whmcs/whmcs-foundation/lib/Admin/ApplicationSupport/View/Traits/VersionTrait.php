<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Admin\ApplicationSupport\View\Traits;

// Decoded file for php version 72.
trait VersionTrait
{
    private $version;
    public function getFeatureVersion()
    {
        $version = $this->getVersion();
        return $version->getMajor() . "." . $version->getMinor();
    }
    public function getVersion()
    {
        if(!$this->version) {
            $app = \DI::make("app");
            $this->version = $app->getVersion();
        }
        return $this->version;
    }
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }
}

?>