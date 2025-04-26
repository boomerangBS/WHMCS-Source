<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Admin\Formatter;

class Client
{
    protected $client;
    protected $color;
    protected $isLink = true;
    protected $openNewWindow = false;
    protected static $clientGroups;
    const DISPLAY_NAME_ONLY = 1;
    const DISPLAY_COMPANY_OR_NAME = 2;
    const DISPLAY_COMPANY_AND_NAME = 3;
    public function __construct(\WHMCS\User\Client $client)
    {
        $this->client = $client;
    }
    public function groupColor($color) : \self
    {
        $this->color = $color;
        return $this;
    }
    public function openWindow() : \self
    {
        $this->openNewWindow = true;
        return $this;
    }
    public function noLink() : \self
    {
        $this->isLink = false;
        return $this;
    }
    public function getLabel()
    {
        $label = "";
        $this->getClientDisplayFormat();
        switch ($this->getClientDisplayFormat()) {
            case self::DISPLAY_COMPANY_OR_NAME:
                $label = scoalesce($this->client->companyName, $this->client->fullName);
                break;
            case self::DISPLAY_COMPANY_AND_NAME:
                $label = $this->client->fullName;
                if(0 < strlen($this->client->companyName)) {
                    $label .= " (" . $this->client->companyName . ")";
                }
                break;
            case self::DISPLAY_NAME_ONLY:
                $label = $this->client->fullName;
                break;
            default:
                return $label;
        }
    }
    public function markup()
    {
        $label = $this->getLabel();
        $this->defaults();
        if($this->color) {
            $label = sprintf("<span style=\"background: %s;\">%s</span>", $this->color, $label);
        }
        if($this->isLink) {
            $newWindow = $this->openNewWindow ? "target=\"_blank\" rel=\"noreferrer noopener\"" : "";
            $label = sprintf("<a href=\"%s/clientssummary.php?userid=%d\" %s>%s</a>", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl(), $this->client->id, $newWindow, $label);
        }
        return $label;
    }
    protected function defaults() : \self
    {
        $this->defaultColor();
        return $this;
    }
    protected function defaultColor() : \self
    {
        if(is_null($this->color) && 0 < $this->client->groupId) {
            $this->color = $this->getGroupColor($this->client->groupId);
        }
        return $this;
    }
    public function getGroupColor(int $clientGroupId)
    {
        return $this->getClientGroups()[$clientGroupId]["colour"];
    }
    public function getClientGroups() : array
    {
        if(!is_array(self::$clientGroups)) {
            self::$clientGroups = getClientGroups();
        }
        return self::$clientGroups;
    }
    protected function getClientDisplayFormat()
    {
        return \WHMCS\Config\Setting::getValue("ClientDisplayFormat") ?? self::DISPLAY_NAME_ONLY;
    }
}

?>