<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Event\Traits;

trait InvoiceAwareTrait
{
    private $invoice;
    public function invoice() : \WHMCS\Billing\Invoice
    {
        return $this->invoice;
    }
    public function setInvoice(\WHMCS\Billing\Invoice $invoice) : \self
    {
        $this->invoice = $invoice;
        return $this;
    }
    protected function hasInvoice()
    {
        return !is_null($this->invoice);
    }
    protected function assertInvoice() : \self
    {
        if(!$this->hasInvoice()) {
            throw \WHMCS\Payment\Exception\MissingRequirement::ofImplementor("invoice", self::class);
        }
        return $this;
    }
}

?>