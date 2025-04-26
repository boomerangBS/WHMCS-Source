<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Notification;

class VersionFeatureHighlights
{
    protected $version;
    protected $incrementalVersion;
    const FEATURE_HIGHLIGHT_VERSION = "8.12.0-rc.1";
    public function __construct($featureVersion = self::FEATURE_HIGHLIGHT_VERSION, \WHMCS\Updater\Version\IncrementalVersion $incrementalVersion = NULL)
    {
        $this->version = $featureVersion;
        if(is_null($incrementalVersion)) {
            $this->incrementalVersion = \WHMCS\Updater\Version\IncrementalVersion::factory($this->version);
        } else {
            $this->incrementalVersion = $incrementalVersion;
        }
        return $this;
    }
    public function getFeatureHighlights()
    {
        $highlights = $this->incrementalVersion->getFeatureHighlights();
        if(empty($highlights)) {
            throw new \WHMCS\Exception("No highlights returned for: " . $this->version);
        }
        return $highlights;
    }
}

?>