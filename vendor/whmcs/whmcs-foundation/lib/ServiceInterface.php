<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
interface ServiceInterface
{
    public function getServiceClient() : User\Client;
    public function getServiceDomain();
    public function getServiceProperties() : Service\Properties;
    public function getServiceActual() : Service\Service;
    public function getServiceSurrogate() : Service\Service;
    public function hasServiceSurrogate();
    public function getServiceProduct();
    public function isServiceMetricUsage();
}

?>