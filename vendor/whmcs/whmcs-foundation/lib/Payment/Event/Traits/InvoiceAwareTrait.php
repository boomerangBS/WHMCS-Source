<?php

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