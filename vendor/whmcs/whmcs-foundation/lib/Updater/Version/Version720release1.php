<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version720release1 extends IncrementalVersion
{
    protected $updateActions = ["renameClientIdToUseridInTblHostingAddons"];
    protected function renameClientIdToUseridInTblHostingAddons()
    {
        $schemaBuilder = \WHMCS\Database\Capsule::schema();
        if($schemaBuilder->hasColumn("tblhostingaddons", "client_id")) {
            \WHMCS\Database\Capsule::connection()->statement("ALTER TABLE tblhostingaddons CHANGE client_id userid int(10) NOT NULL DEFAULT '0'");
        }
    }
}

?>