<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Config;

interface DatabaseInterface
{
    public function getDatabaseName();
    public function getDatabaseUsername();
    public function getDatabasePassword();
    public function getDatabaseHost();
    public function getDatabaseCharset();
    public function getDatabasePort();
    public function setDatabasePort($value);
    public function setDatabaseName($value);
    public function setDatabaseUsername($value);
    public function setDatabasePassword($value);
    public function setDatabaseHost($value);
    public function setDatabaseCharset($value);
    public function getSqlMode();
    public function getDatabaseOptions() : array;
    public function setDatabaseOptions(array $options);
}

?>