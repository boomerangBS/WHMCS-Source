<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Scheduling;

interface StatusInterface
{
    public function isInProgress();
    public function isDueNow();
    public function calculateAndSetNextDue();
    public function setNextDue(\WHMCS\Carbon $nextDue);
    public function setInProgress($state);
    public function getLastRuntime();
    public function setLastRuntime(\WHMCS\Carbon $date);
    public function getNextDue();
}

?>