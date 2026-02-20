<?php

namespace WHMCS\Model\Traits;

trait SchemaVersionTrait
{
    protected static $schemaVersion;
    public static function originSchemaVersion() : int
    {
        return 0;
    }
    public static function activeSchemaVersion() : int
    {
        if(is_null(static::$schemaVersion)) {
            return static::latestSchemaVersion();
        }
        return static::$schemaVersion;
    }
    public static function useSchemaVersion($version) : int
    {
        $previous = static::activeSchemaVersion();
        static::$schemaVersion = $version;
        return $previous;
    }
    public static function isSchemaVersion($version) : int
    {
        return static::activeSchemaVersion() == $version;
    }
    public static function isAtLeastSchemaVersion($version) : int
    {
        return $version <= static::activeSchemaVersion();
    }
    public static function useOriginSchema() : int
    {
        $previous = static::activeSchemaVersion();
        static::$schemaVersion = static::originSchemaVersion();
        return $previous;
    }
    public static function resetSchemaVersion() : int
    {
        return static::useSchemaVersion(static::latestSchemaVersion());
    }
}

?>