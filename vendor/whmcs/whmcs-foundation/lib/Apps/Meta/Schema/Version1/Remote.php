<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Apps\Meta\Schema\Version1;

class Remote extends Local
{
    public function getLogoAssetFilename()
    {
        return $this->meta("logo.asset_filename");
    }
    public function getLogoRemoteUri()
    {
        return $this->meta("logo.remote_uri");
    }
    public function getPurchaseFreeTrialDays()
    {
        return $this->meta("purchase.freeTrialDays");
    }
    public function getPurchasePrice()
    {
        return $this->meta("purchase.price");
    }
    public function getPurchaseCurrency()
    {
        return $this->meta("purchase.currency");
    }
    public function getPurchaseTerm()
    {
        return $this->meta("purchase.term");
    }
    public function getPurchaseUrl()
    {
        return $this->meta("purchase.url");
    }
    public function isFeatured()
    {
        return (bool) $this->meta("badges.featured");
    }
    public function isPopular()
    {
        return (bool) $this->meta("badges.popular");
    }
    public function isUpdated()
    {
        return (bool) $this->meta("badges.updated");
    }
    public function isNew()
    {
        return (bool) $this->meta("badges.new");
    }
    public function isDeprecated()
    {
        return (bool) $this->meta("badges.deprecated");
    }
    public function getKeywords()
    {
        return $this->meta("keywords");
    }
    public function getWeighting()
    {
        return (int) $this->meta("weighting");
    }
    public function isHidden()
    {
        return (bool) $this->meta("hidden");
    }
}

?>