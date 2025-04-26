<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Fraud\MaxMind;

class Request extends \WHMCS\Module\Fraud\AbstractRequest implements \WHMCS\Module\Fraud\RequestInterface
{
    protected $accountId;
    protected $licenseKey;
    protected $serviceType;
    protected $useSandbox = false;
    const URL = "https://minfraud.maxmind.com/minfraud/v2.0/";
    const URL_SANDBOX = "https://sandbox.maxmind.com/minfraud/v2.0/";
    public function useSandbox($b) : \self
    {
        $this->useSandbox = $b;
        return $this;
    }
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }
    public function setLicenseKey($licenseKey)
    {
        $this->licenseKey = $licenseKey;
        return $this;
    }
    public function setServiceType($serviceType)
    {
        $serviceType = strtolower($serviceType);
        if(!in_array($serviceType, ["score", "insights", "factors"])) {
            throw new \Exception("Invalid service type: " . $serviceType);
        }
        $this->serviceType = $serviceType;
        return $this;
    }
    public function call($data)
    {
        $client = $this->getClient();
        $response = $client->post($this->getApiEndpointUrl(), ["auth" => [$this->accountId, $this->licenseKey], \GuzzleHttp\RequestOptions::HTTP_ERRORS => false, "json" => $data]);
        $maxmindResponse = new Response($response->getBody(), $response->getStatusCode());
        $this->log("check", $data, $response, $maxmindResponse->toArray());
        if($maxmindResponse->isEmpty()) {
            throw new \WHMCS\Exception\Http\ConnectionError($response->getBody());
        }
        return $maxmindResponse;
    }
    protected function getApiEndpointUrl()
    {
        return sprintf("%s%s", $this->useSandbox ? self::URL_SANDBOX : self::URL, $this->serviceType);
    }
}

?>