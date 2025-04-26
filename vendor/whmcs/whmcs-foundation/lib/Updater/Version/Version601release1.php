<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version601release1 extends IncrementalVersion
{
    protected $updateActions = ["migrateFixedInvoiceDataAddon"];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove[] = ROOTDIR . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "addons" . DIRECTORY_SEPARATOR . "fixed_invoice_data";
    }
    protected function migrateFixedInvoiceDataAddon()
    {
        $fixedInvoiceDataSettings = \Illuminate\Database\Capsule\Manager::table("tbladdonmodules")->where("module", "fixed_invoice_data")->count();
        if(0 < $fixedInvoiceDataSettings) {
            \WHMCS\Config\Setting::setValue("StoreClientDataSnapshotOnInvoiceCreation", "on");
            $fixedInvoiceDataSettings = \Illuminate\Database\Capsule\Manager::table("tbladdonmodules")->where("module", "fixed_invoice_data")->delete();
        }
        return $this;
    }
}

?>