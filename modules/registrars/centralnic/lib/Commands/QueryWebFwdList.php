<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

class QueryWebFwdList extends AbstractCommand
{
    protected $command = "QueryWebFwdList";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $domain, string $hostName)
    {
        $this->setParam("dnszone", $domain)->setParam("source", $hostName == "@" ? $domain : $hostName . "." . $domain)->setParam("wide", 1);
        parent::__construct($api);
    }
}

?>