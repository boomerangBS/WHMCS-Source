<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Exception\Handler;

trait ExceptionLoggingTrait
{
    public function log($exception)
    {
        try {
            $isLogHandlerLoaded = false;
            $logger = \Log::self();
            foreach ($logger->getHandlers() as $logHandler) {
                if($logHandler instanceof Log\BaseExceptionLoggerHandler) {
                    $isLogHandlerLoaded = true;
                }
            }
            if(!$isLogHandlerLoaded) {
                $logger->pushHandler(new Log\BaseExceptionLoggerHandler());
                $logger->pushHandler(new Log\ErrorExceptionLoggerHandler());
                $logger->pushHandler(new Log\PdoExceptionLoggerHandler());
            }
            $logger->error((string) $exception, ["exception" => $exception]);
        } catch (\Exception $e) {
        } catch (\Error $e) {
        }
    }
}

?>