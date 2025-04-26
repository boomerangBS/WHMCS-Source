<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing\Observers;

class InvoiceItemObserver
{
    public function created(\WHMCS\Billing\Invoice\Item $item)
    {
        $this->touchParent($item);
    }
    public function deleted(\WHMCS\Billing\Invoice\Item $item)
    {
        $this->touchParent($item);
    }
    public function forceDeleted(\WHMCS\Billing\Invoice\Item $item)
    {
        $this->touchParent($item);
    }
    public function updated(\WHMCS\Billing\Invoice\Item $item)
    {
        $this->touchParent($item);
    }
    protected function touchParent(\WHMCS\Billing\Invoice\Item $item)
    {
        if($item->invoiceId) {
            $item->touchOwners();
        }
    }
}

?>