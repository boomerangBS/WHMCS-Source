<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\MarketConnect\Ssl;

// Decoded file for php version 72.
class ApiRenewResult extends ApiResult
{
    protected $dcvEmail;
    public function __construct(array $raw, string $dcvEmail = NULL)
    {
        parent::__construct($raw);
        if($this->hasValidationMethod(\WHMCS\Service\Ssl::DOMAIN_VALIDATION_EMAIL) && parent::hasEmailAuthDetails()) {
            $this->dcvEmail = $this->getDcvValues()["email"];
        } else {
            $this->dcvEmail = $dcvEmail;
        }
    }
    protected function newValidationMethodEmail() : \WHMCS\Service\Ssl\ValidationMethodEmailauth
    {
        $method = new \WHMCS\Service\Ssl\ValidationMethodEmailauth();
        $method->email = $this->dcvEmail;
        return $method;
    }
    protected function hasEmailAuthDetails()
    {
        return 0 < strlen($this->dcvEmail);
    }
}

?>