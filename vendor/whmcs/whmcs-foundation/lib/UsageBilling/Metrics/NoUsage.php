<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Metrics;

class NoUsage implements \WHMCS\UsageBilling\Contracts\Metrics\UsageStubInterface
{
    private $now;
    public function __construct()
    {
        $this->now = \WHMCS\Carbon::now();
    }
    public function collectedAt()
    {
        return $this->now->copy();
    }
    public function startAt()
    {
        return $this->now->copy();
    }
    public function endAt()
    {
        return $this->now->copy();
    }
    public function value()
    {
        return 0;
    }
}

?>