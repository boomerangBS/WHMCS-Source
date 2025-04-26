<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

class AddWebFwd extends AbstractCommand
{
    protected $command = "AddWebFwd";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $domain, string $hostName, string $target, string $type)
    {
        $this->setParam("source", $hostName == "@" ? $domain : $hostName . "." . $domain)->setParam("target", $target)->setParam("type", $type == "URL" ? "RD" : "MRD");
        parent::__construct($api);
    }
}

?>