<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class CheckDNSZone extends AbstractCommand
{
    protected $command = "CheckDNSZone";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $domain)
    {
        $this->setParam("dnszone", $domain);
        parent::__construct($api);
    }
    public function handleResponse(\WHMCS\Module\Registrar\CentralNic\Api\Response $response) : \WHMCS\Module\Registrar\CentralNic\Api\Response
    {
        if(200 <= $response->getCode() && $response->getCode() <= 300) {
            return $response;
        }
        throw new \Exception($response->getDescription(), $response->getCode());
    }
}

?>