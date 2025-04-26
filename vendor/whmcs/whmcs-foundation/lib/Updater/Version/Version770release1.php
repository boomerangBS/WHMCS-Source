<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version770release1 extends IncrementalVersion
{
    protected $updateActions = ["registerSslStatusSyncCronTask"];
    public function registerSslStatusSyncCronTask()
    {
        \WHMCS\Cron\Task\SslStatusSync::register();
        return $this;
    }
}

?>