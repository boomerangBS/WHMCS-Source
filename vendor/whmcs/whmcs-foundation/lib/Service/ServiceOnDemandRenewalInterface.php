<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service;

interface ServiceOnDemandRenewalInterface
{
    public function isRenewable();
    public function renew($amount, string $paymentMethod) : \WHMCS\Billing\Invoice\Item;
    public function getBillingCycle();
    public function getNextPayUntilDate() : \Carbon\CarbonInterface;
    public function getPrice() : \WHMCS\View\Formatter\Price;
    public function getService();
    public function getProduct();
    public function isTaxable();
    public function getServiceId() : int;
    public function getProductName();
    public function getReason();
}

?>