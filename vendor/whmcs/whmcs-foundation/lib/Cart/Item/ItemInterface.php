<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cart\Item;

interface ItemInterface
{
    public function getUuid();
    public function setId(string $id);
    public function getId();
    public function setName(string $name);
    public function getName();
    public function setBillingCycle($billingCycle);
    public function getBillingCycle();
    public function setBillingPeriod(string $billingPeriod);
    public function getBillingPeriod();
    public function setQuantity(int $qty);
    public function getQuantity();
    public function setAmount(\WHMCS\View\Formatter\Price $amount);
    public function getAmount();
    public function setRecurringAmount(\WHMCS\View\Formatter\Price $recurring);
    public function getRecurringAmount();
    public function setTaxed($taxed);
    public function isTaxed();
    public function setInitialPeriod($period, $cycle);
    public function hasInitialPeriod();
    public function isRecurring();
    public function getType();
}

?>