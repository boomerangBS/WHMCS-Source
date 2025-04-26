<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cron\Task;

class RefreshAppsFeed extends \WHMCS\Scheduling\Task\AbstractTask
{
    protected $accessLevel = \WHMCS\Scheduling\Task\TaskInterface::ACCESS_SYSTEM;
    protected $defaultPriority = 1680;
    protected $defaultFrequency = 1440;
    protected $defaultDescription = "Refresh Apps & Integrations Feed Cache";
    protected $defaultName = "Refresh Apps Feed";
    protected $systemName = "RefreshAppsFeed";
    protected $icon = "fas fa-refresh";
    protected $successCountIdentifier = "processed";
    protected $successKeyword = "Completed";
    public function __invoke() : \self
    {
        new \WHMCS\Apps\Feed();
        return $this;
    }
}

?>