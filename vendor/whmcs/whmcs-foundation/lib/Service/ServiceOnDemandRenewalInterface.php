<?php

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