<?php


namespace WHMCS;
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