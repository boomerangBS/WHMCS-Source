<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Model\Contracts;

interface SchemaVersionAware
{
    public static function latestSchemaVersion() : int;
    public static function activeSchemaVersion() : int;
    public static function isSchemaVersion($version) : int;
    public static function isAtLeastSchemaVersion($version) : int;
    public static function useSchemaVersion($version) : int;
    public static function useOriginSchema() : int;
    public static function resetSchemaVersion() : int;
}

?>