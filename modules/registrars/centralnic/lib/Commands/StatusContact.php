<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

class StatusContact extends AbstractCommand
{
    protected $command = "StatusContact";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $contactHandle)
    {
        $this->setParam("contact", $contactHandle);
        parent::__construct($api);
    }
}

?>