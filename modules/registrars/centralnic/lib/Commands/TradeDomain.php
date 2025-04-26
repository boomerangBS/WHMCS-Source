<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

class TradeDomain extends AbstractCommand
{
    protected $command = "TradeDomain";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $domain, string $newOwnerContact)
    {
        $this->setParam("domain", $domain)->setParam("ownercontact0", $newOwnerContact);
        parent::__construct($api);
    }
}

?>