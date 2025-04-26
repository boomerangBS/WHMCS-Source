<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User;

class Alert
{
    protected $message;
    protected $severity = "info";
    protected $link;
    protected $linkText;
    public function __construct($message, $severity = "info", $link = NULL, $linkText = NULL)
    {
        $this->setMessage($message)->setSeverity($severity)->setLink($link)->setLinkText($linkText);
    }
    public function getMessage()
    {
        return $this->message;
    }
    protected function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    public function getSeverity()
    {
        return $this->severity;
    }
    protected function setSeverity($severity = "info")
    {
        if(!in_array($severity, ["success", "info", "warning", "danger"])) {
            throw new \WHMCS\Exception("Please set an alert's severity to either \"success\", \"info\", \"warning\", or \"danger\".");
        }
        $this->severity = $severity;
        return $this;
    }
    public function getLink()
    {
        return $this->link;
    }
    protected function setLink($link)
    {
        $this->link = $link;
        return $this;
    }
    public function getLinkText()
    {
        return $this->linkText;
    }
    protected function setLinkText($linkText)
    {
        $this->linkText = $linkText;
        return $this;
    }
}

?>