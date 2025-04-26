<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version790release1 extends IncrementalVersion
{
    protected $updateActions = ["pruneOrphanedSslOrders"];
    public function pruneOrphanedSslOrders()
    {
        $orphanedSslOrderIds = \WHMCS\Database\Capsule::table("tblsslorders")->leftJoin("tblhostingaddons", "tblsslorders.addon_id", "=", "tblhostingaddons.id")->whereNull("tblhostingaddons.id")->where("tblsslorders.addon_id", "!=", 0)->pluck("tblsslorders.id")->all();
        \WHMCS\Database\Capsule::table("tblsslorders")->whereIn("id", $orphanedSslOrderIds)->delete();
        return $this;
    }
}

?>