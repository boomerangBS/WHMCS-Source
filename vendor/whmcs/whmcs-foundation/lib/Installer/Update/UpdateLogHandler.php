<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Installer\Update;

class UpdateLogHandler extends \Monolog\Handler\AbstractProcessingHandler
{
    protected function write($record) : void
    {
        $instanceId = "not defined";
        if(isset($record["context"]["instance_id"])) {
            $instanceId = $record["context"]["instance_id"];
        } elseif($storedId = \WHMCS\Config\Setting::getValue("UpdaterUpdateToken")) {
            $instanceId = $storedId;
        }
        if(!isset($record["extra"])) {
            $record["extra"] = [];
        }
        if(trim($record["formatted"])) {
            $logEntry = new UpdateLog();
            $logEntry->message = $record["formatted"];
            $logEntry->instance_id = $instanceId;
            $logEntry->level = $record["level"];
            $logEntry->extra = json_encode($record["extra"]);
            $logEntry->save();
        }
    }
    protected function getDefaultFormatter() : \Monolog\Formatter\FormatterInterface
    {
        return new \Monolog\Formatter\LineFormatter("%message%");
    }
}

?>