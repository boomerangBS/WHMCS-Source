<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error;

trait ErrorLevelTrait
{
    protected $errorLevel = ErrorLevelInterface::ERROR;
    public function isAnError()
    {
        return ErrorLevelInterface::ERROR <= $this->errorLevel;
    }
    public function errorName()
    {
        return ucfirst(strtolower(\Monolog\Logger::getLevelName($this->errorLevel)));
    }
}

?>