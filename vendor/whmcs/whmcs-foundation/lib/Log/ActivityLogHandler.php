<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Log;

class ActivityLogHandler extends \Monolog\Handler\AbstractProcessingHandler
{
    protected function write($record) : void
    {
        if($record["formatted"]) {
            try {
                $event = ["date" => (string) \WHMCS\Carbon::now()->format("YmdHis"), "description" => $record["formatted"], "user" => "", "userid" => "", "ipaddr" => ""];
                \WHMCS\Database\Capsule::table("tblactivitylog")->insertGetId($event);
            } catch (\Exception $e) {
            }
        }
    }
}

?>