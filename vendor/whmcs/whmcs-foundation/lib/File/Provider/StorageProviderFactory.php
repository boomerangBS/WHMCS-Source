<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\File\Provider;

class StorageProviderFactory
{
    private static $providerClasses = ["WHMCS\\File\\Provider\\LocalStorageProvider", "WHMCS\\File\\Provider\\S3StorageProvider"];
    public static function getProviderClasses()
    {
        $providers = [];
        foreach (self::$providerClasses as $providerClass) {
            $providers[$providerClass::getShortName()] = $providerClass;
        }
        return $providers;
    }
    public static function createProvider($providerShortName)
    {
        $providers = self::getProviderClasses();
        if(array_key_exists($providerShortName, $providers)) {
            return new $providers[$providerShortName]();
        }
        return NULL;
    }
    public static function getLocalStoragePathsInUse()
    {
        $paths = [];
        foreach (static::getStorageConfigurationsInUse() as $config) {
            $provider = $config->createStorageProvider();
            if($provider->isLocal()) {
                $paths[] = $provider->getLocalPath();
            }
        }
        return $paths;
    }
    public static function getStorageConfigurationsInUse() : array
    {
        $configurations = [];
        foreach (\WHMCS\File\Configuration\StorageConfiguration::has("assetSettings")->get() as $config) {
            $configurations[] = $config;
        }
        return $configurations;
    }
    public static function getTopLevelLocalStoragePathsInUse()
    {
        $localStoragePaths = static::getLocalStoragePathsInUse();
        $uniqueTopLevelPaths = [];
        foreach ($localStoragePaths as $storagePath) {
            foreach ($uniqueTopLevelPaths as &$topLevelPath) {
                if(strpos($topLevelPath . DIRECTORY_SEPARATOR, $storagePath . DIRECTORY_SEPARATOR) === 0) {
                    $topLevelPath = $storagePath;
                } elseif(strpos($storagePath . DIRECTORY_SEPARATOR, $topLevelPath . DIRECTORY_SEPARATOR) === 0) {
                }
            }
            unset($topLevelPath);
            $uniqueTopLevelPaths[] = $storagePath;
        }
        return $uniqueTopLevelPaths;
    }
}

?>