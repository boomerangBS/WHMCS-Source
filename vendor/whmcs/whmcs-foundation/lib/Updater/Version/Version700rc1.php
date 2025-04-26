<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version700rc1 extends IncrementalVersion
{
    protected $updateActions = ["setDefaultUpdateDetails", "setDefaultDailyCronInvocationHour"];
    public function setDefaultUpdateDetails()
    {
        \WHMCS\Config\Setting::setValue("UpdaterLatestVersion", \WHMCS\Application::FILES_VERSION);
        \WHMCS\Config\Setting::setValue("UpdaterLatestBetaVersion", \WHMCS\Application::FILES_VERSION);
        \WHMCS\Config\Setting::setValue("UpdaterLatestStableVersion", \WHMCS\Application::FILES_VERSION);
        \WHMCS\Config\Setting::setValue("UpdaterLatestSupportAndUpdatesVersion", \WHMCS\Application::FILES_VERSION);
        return $this;
    }
    public function setDefaultDailyCronInvocationHour()
    {
        \WHMCS\Cron\Status::setDailyCronExecutionHour();
    }
}

?>