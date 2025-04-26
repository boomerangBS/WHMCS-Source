<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

class ValidationItem
{
    protected $name = "";
    protected $value;
    protected $rule = "";
    protected $assertionMessage = "";
    public function __construct(string $name, $value, string $rule)
    {
        $this->name = $name;
        $this->value = $value;
        $this->rule = $rule;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function getRule()
    {
        return $this->rule;
    }
    public function getAssertionMessage()
    {
        return $this->assertionMessage;
    }
    public function setAssertionMessage($message) : \self
    {
        $this->assertionMessage = $message;
        return $this;
    }
}

?>