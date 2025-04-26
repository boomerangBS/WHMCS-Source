<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service\Ssl;

class ValidationMethodDnsauth extends ValidationMethod
{
    public $type;
    public $host;
    public $value;
    public function methodNameConstant()
    {
        return \WHMCS\Service\Ssl::DOMAIN_VALIDATION_DNS;
    }
    public function friendlyName()
    {
        return "DNS";
    }
    public function translationKey(\WHMCS\Language\AbstractLanguage $language) : \WHMCS\Language\AbstractLanguage
    {
        if($language instanceof \WHMCS\Language\AdminLanguage) {
            return "wizard.ssl.dnsMethod";
        }
        return "ssl.dnsMethod";
    }
    public function populate($values) : ValidationMethod
    {
        return $this->populateFromClassProperties($values);
    }
    public function defaults() : ValidationMethod
    {
        $this->type = $this->getRecordTypeWithDefault(static::getRecordTypeDefault());
        $this->host = $this->getHostWithDefault(static::getHostDefault());
        return $this;
    }
    public function getRecordTypeWithDefault($default)
    {
        return ecoalesce($this->type, $default);
    }
    public static function getRecordTypeDefault()
    {
        return "TXT";
    }
    public function getHostWithDefault($default)
    {
        return ecoalesce($this->host, $default);
    }
    public static function getHostDefault()
    {
        return "_dnsauth";
    }
}

?>