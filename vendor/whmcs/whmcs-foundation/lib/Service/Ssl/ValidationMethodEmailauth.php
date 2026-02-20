<?php

namespace WHMCS\Service\Ssl;

class ValidationMethodEmailauth extends ValidationMethod
{
    public $email;
    public function methodNameConstant()
    {
        return \WHMCS\Service\Ssl::DOMAIN_VALIDATION_EMAIL;
    }
    public function friendlyName()
    {
        return "Email";
    }
    public function translationKey(\WHMCS\Language\AbstractLanguage $language) : \WHMCS\Language\AbstractLanguage
    {
        if($language instanceof \WHMCS\Language\AdminLanguage) {
            return "wizard.ssl.emailMethod";
        }
        return "ssl.emailMethod";
    }
    public function populate($values) : ValidationMethod
    {
        return $this->populateFromClassProperties($values);
    }
    public function defaults() : ValidationMethod
    {
        return $this;
    }
}

?>