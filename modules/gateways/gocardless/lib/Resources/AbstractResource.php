<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\GoCardless\Resources;

class AbstractResource
{
    protected $params = [];
    protected $client;
    public function __construct(array $gatewayParams)
    {
        $this->params = $gatewayParams;
        $this->client = \WHMCS\Module\Gateway\GoCardless\Client::factory($gatewayParams["accessToken"]);
    }
}

?>