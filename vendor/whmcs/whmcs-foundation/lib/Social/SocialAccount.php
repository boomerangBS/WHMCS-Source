<?php

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