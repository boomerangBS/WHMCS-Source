<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version761release1 extends IncrementalVersion
{
    protected $updateActions = ["correctWhmcsWhoisToWhmcsDomains"];
    protected function correctWhmcsWhoisToWhmcsDomains()
    {
        $query = \WHMCS\Database\Capsule::table("tblconfiguration")->where("setting", "domainLookupProvider");
        if(!$query->count()) {
            \WHMCS\Config\Setting::setValue("domainLookupProvider", "WhmcsDomains");
        } else {
            $settingNotConverted = \WHMCS\Database\Capsule::table("tblconfiguration")->where("setting", "domainLookupProvider")->whereIn("value", ["BasicWhois", "WhmcsWhois", ""])->where("updated_at", "<", "2018-06-28 00:00:00")->first();
            if($settingNotConverted) {
                \WHMCS\Config\Setting::setValue("domainLookupProvider", "WhmcsDomains");
            }
        }
        return $this;
    }
}

?>