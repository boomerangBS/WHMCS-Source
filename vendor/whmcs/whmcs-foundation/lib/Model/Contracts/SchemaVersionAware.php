<?php

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