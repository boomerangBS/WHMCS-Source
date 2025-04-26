<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cart\OrderForm;

interface RenewalsInterface
{
    public function addDomain($domainId, int $renewalPeriod) : void;
    public function addService(\WHMCS\Service\ServiceOnDemandRenewal $onDemandRenewal) : void;
    public function addServiceAddon(\WHMCS\Service\ServiceOnDemandRenewal $onDemandRenewal) : void;
    public function removeDomain($domainId) : void;
    public function removeService(\WHMCS\Service\ServiceOnDemandRenewal $onDemandRenewal) : void;
    public function removeServiceAddon(\WHMCS\Service\ServiceOnDemandRenewal $onDemandRenewal) : void;
    public function getDomains() : array;
    public function getServices() : array;
    public function getServiceAddons() : array;
}

?>