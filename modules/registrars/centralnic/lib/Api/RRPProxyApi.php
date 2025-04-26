<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Api;

class RRPProxyApi extends AbstractApi
{
    protected $customHeader = "";
    public function getCustomHeader()
    {
        return $this->customHeader;
    }
    public function setCustomHeader($header) : \self
    {
        $this->customHeader = $header;
        return $this;
    }
}

?>