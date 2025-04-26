<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version771release1 extends IncrementalVersion
{
    protected $updateActions = ["addIndexToSslSyncTable"];
    public function addIndexToSslSyncTable()
    {
        $domainNameIndex = \WHMCS\Database\Capsule::connection()->select("SHOW INDEX FROM `tblsslstatus` WHERE Column_name=\"domain_name\"");
        if(empty($domainNameIndex)) {
            \WHMCS\Database\Capsule::schema()->table((new \WHMCS\Domain\Ssl\Status())->getTable(), function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->index("domain_name", "domain_name");
            });
        }
        return $this;
    }
}

?>