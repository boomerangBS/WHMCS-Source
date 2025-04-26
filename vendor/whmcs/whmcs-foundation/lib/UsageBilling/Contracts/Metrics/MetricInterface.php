<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Contracts\Metrics;

interface MetricInterface
{
    const TYPE_SNAPSHOT = "snapshot";
    const TYPE_PERIOD_DAY = "day";
    const TYPE_PERIOD_MONTH = "month";
    public function usage();
    public function withUsage(UsageInterface $usage);
    public function units();
    public function systemName();
    public function displayName();
    public function type();
}

?>