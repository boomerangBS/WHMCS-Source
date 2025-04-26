<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\File\Provider;

interface StorageProviderInterface
{
    public static function getShortName();
    public static function getName();
    public function getConfigSummaryText();
    public function getConfigSummaryHtml();
    public function getIcon();
    public function applyConfiguration(array $configSettings);
    public function testConfiguration();
    public function exportConfiguration(\WHMCS\File\Configuration\StorageConfiguration $config);
    public function getConfigurationFields();
    public function getAccessCredentialFieldNames();
    public function getFieldsLockedInUse();
    public function isLocal();
    public function createFilesystemAdapterForAssetType($assetType, $subPath);
    public static function getExceptionErrorMessage(\Exception $e);
}

?>