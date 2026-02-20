<?php

namespace WHMCS\Exception\Handler\Log;

class ErrorExceptionLoggerHandler extends \WHMCS\Log\ActivityLogHandler
{
    public function isHandling($record) : array
    {
        if(parent::isHandling($record)) {
            return \WHMCS\Utility\ErrorManagement::isAllowedToLogErrors();
        }
        return false;
    }
    protected function write($record) : void
    {
        $exception = $record["context"]["exception"];
        if($exception instanceof \Error || $exception instanceof \ErrorException) {
            parent::write($record);
        }
    }
    protected function getDefaultFormatter() : \Monolog\Formatter\FormatterInterface
    {
        return new \Monolog\Formatter\LineFormatter("Error: %message%");
    }
}

?>