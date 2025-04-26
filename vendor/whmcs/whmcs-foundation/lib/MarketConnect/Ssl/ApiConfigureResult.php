<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\MarketConnect\Ssl;

// Decoded file for php version 72.
class ApiConfigureResult extends ApiResult
{
    protected $payload;
    public function __construct(array $raw, Configuration $payload)
    {
        parent::__construct($raw);
        $this->payload = $payload;
    }
    protected function newValidationMethodEmail() : \WHMCS\Service\Ssl\ValidationMethodEmailauth
    {
        $method = new \WHMCS\Service\Ssl\ValidationMethodEmailauth();
        $method->email = $this->payload->getDomainValidationEmail();
        return $method;
    }
    protected function hasEmailAuthDetails()
    {
        return 0 < strlen($this->payload->getDomainValidationEmail());
    }
}

?>