<?php

namespace WHMCS\Billing\Invoice;

interface InvoicingServiceInterface
{
    public function getInvoicingServiceItemType();
    public function getInvoicingServiceFirstPaymentAmount() : \WHMCS\View\Formatter\Price;
    public function getInvoicingServiceRecurringAmount() : \WHMCS\View\Formatter\Price;
}

?>