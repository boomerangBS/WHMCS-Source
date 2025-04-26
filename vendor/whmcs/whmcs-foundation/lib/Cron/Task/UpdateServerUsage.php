<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cron\Task;

class UpdateServerUsage extends \WHMCS\Scheduling\Task\AbstractTask
{
    protected $defaultPriority = 1660;
    protected $defaultFrequency = 1440;
    protected $defaultDescription = "Updating Disk & Bandwidth Usage Stats";
    protected $defaultName = "Server Usage Stats";
    protected $systemName = "UpdateServerUsage";
    protected $outputs = ["updated" => ["defaultValue" => 0, "identifier" => "updated", "name" => "Servers Updated"], "completed" => ["defaultValue" => 0, "identifier" => "completed", "name" => "Server Usage Updates Completed"]];
    protected $icon = "fas fa-server";
    protected $isBooleanStatus = true;
    protected $successCountIdentifier = "completed";
    public function __invoke()
    {
        if(!function_exists("ServerUsageUpdate")) {
            include_once ROOTDIR . "/includes/modulefunctions.php";
        }
        if(!\WHMCS\Config\Setting::getValue("UpdateStatsAuto")) {
            return true;
        }
        $updatedServerIds = ServerUsageUpdate();
        $this->output("updated")->write(count($updatedServerIds));
        $this->output("completed")->write(1);
        return $this;
    }
}

?>