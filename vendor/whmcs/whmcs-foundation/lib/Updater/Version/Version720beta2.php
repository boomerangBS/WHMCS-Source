<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version720beta2 extends IncrementalVersion
{
    protected $updateActions = ["addPaymentReversalChangeSettings"];
    protected function addPaymentReversalChangeSettings()
    {
        \WHMCS\Config\Setting::setValue("ReversalChangeInvoiceStatus", 1);
        \WHMCS\Config\Setting::setValue("ReversalChangeDueDates", 1);
        return $this;
    }
}

?>