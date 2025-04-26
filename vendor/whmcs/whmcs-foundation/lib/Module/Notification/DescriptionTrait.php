<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Notification;

trait DescriptionTrait
{
    protected $displayName = "";
    protected $logoFileName = "";
    public function isActive()
    {
        $provider = \WHMCS\Notification\Provider::where("name", "=", $this->getName())->first();
        if(!$provider) {
            return false;
        }
        return (bool) $provider->active;
    }
    public function getName()
    {
        return basename(str_replace("\\", "/", get_class($this)));
    }
    public function getDisplayName()
    {
        return $this->displayName;
    }
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
    public function getLogoFileName()
    {
        return $this->logoFileName;
    }
    public function setLogoFileName($logoFileName)
    {
        $this->logoFileName = $logoFileName;
        return $this;
    }
    public function getLogoPath()
    {
        return "/modules/notifications/" . $this->getName() . "/" . $this->logoFileName;
    }
}

?>