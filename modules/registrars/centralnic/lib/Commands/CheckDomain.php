<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

class CheckDomain extends AbstractCommand
{
    protected $command = "CheckDomain";
    const MAX_TLD_COUNT = 32;
    public function handleResponse(\WHMCS\Module\Registrar\CentralNic\Api\Response $response) : \WHMCS\Module\Registrar\CentralNic\Api\Response
    {
        if(200 <= $response->getCode() && $response->getCode() <= 300) {
            return $response;
        }
        throw new \Exception($response->getDescription(), $response->getCode());
    }
}

?>