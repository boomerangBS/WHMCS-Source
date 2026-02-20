<?php


namespace WHMCS\MarketConnect\Ssl;
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