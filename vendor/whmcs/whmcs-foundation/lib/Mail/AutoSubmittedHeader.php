<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail;

class AutoSubmittedHeader
{
    protected $autoSubmittedValue;
    protected $knownHeaderValues = ["no", "auto-replied", "auto-generated"];
    public function __construct(\ZBateson\MailMimeParser\Message $message)
    {
        if($message->getHeader("Auto-Submitted")) {
            $this->autoSubmittedValue = strtolower($message->getHeader("Auto-Submitted")->getRawValue());
        }
    }
    public function isGenerated()
    {
        return $this->rawData() === "auto-generated";
    }
    public function isReplied()
    {
        return $this->rawData() === "auto-replied";
    }
    public function isValueKnown()
    {
        return in_array($this->rawData(), $this->knownHeaderValues);
    }
    public function isHuman()
    {
        return !$this->exists() || $this->rawData() === "no";
    }
    public function exists()
    {
        return $this->rawData() !== NULL;
    }
    public function isAutomated()
    {
        return $this->isGenerated() || $this->isReplied();
    }
    public function rawData()
    {
        return $this->autoSubmittedValue;
    }
}

?>