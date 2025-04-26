<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Exception\Handler\Log;

class PdoExceptionLoggerHandler extends \WHMCS\Log\ActivityLogHandler
{
    public function isHandling($record) : array
    {
        if(parent::isHandling($record)) {
            return \WHMCS\Utility\ErrorManagement::isAllowedToLogSqlErrors();
        }
        return false;
    }
    protected function write($record) : void
    {
        if($record["context"]["exception"] instanceof \PDOException) {
            parent::write($record);
        }
    }
    protected function getDefaultFormatter() : \Monolog\Formatter\FormatterInterface
    {
        return new \Monolog\Formatter\LineFormatter("PDO Exception: %message%");
    }
}

?>