<?php

namespace WHMCS\Product;

interface PricedEntityInterface
{
    public function isFree();
    public function isOneTime();
    public function getAvailableBillingCycles() : array;
    public function pricing($currency) : Pricing;
}

?>