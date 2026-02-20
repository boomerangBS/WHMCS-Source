<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class AddDNSZone extends AbstractCommand
{
    protected $command = "AddDNSZone";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $domain)
    {
        $this->setParam("dnszone", $domain);
        parent::__construct($api);
    }
}

?>