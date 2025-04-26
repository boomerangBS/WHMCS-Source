<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Installer;

class LogServiceProvider extends \WHMCS\Log\LogServiceProvider
{
    public function factoryDefaultChannelLogger()
    {
        return new \Monolog\Logger("WHMCS Installer");
    }
    protected function importLogHandlers($baseDirectory = NULL)
    {
        parent::importLogHandlers();
        parent::importLogHandlers(INSTALLER_DIR);
        return $this;
    }
    public static function getUpdateLogHandler()
    {
        $updateLogHandler = new Update\UpdateLogHandler(\Monolog\Logger::DEBUG);
        $updateLogHandler->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor());
        $updateLogHandler->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());
        $timer = \WHMCS\Carbon::now();
        $updateLogHandler->pushProcessor(function (array $record) use($timer) {
            $now = \WHMCS\Carbon::now();
            $record["extra"]["time_lapse"] = $timer->diffInSeconds();
            return $record;
        });
        return $updateLogHandler;
    }
}

?>