<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Social;

class SocialAccount
{
    protected $name;
    protected $displayName;
    protected $faIcon;
    protected $configNote;
    protected $value;
    protected $url;
    public function __construct($name, $displayName, $faIcon, $configNote, $value, $url)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->faIcon = $faIcon;
        $this->configNote = $configNote;
        $this->value = $value;
        $this->url = $url;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getDisplayName()
    {
        return $this->displayName;
    }
    public function getFontAwesomeIcon()
    {
        return "fab " . $this->faIcon;
    }
    public function getConfigNote()
    {
        return $this->configNote;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function getUrl()
    {
        return str_replace("{id}", $this->getValue(), $this->url);
    }
}

?>