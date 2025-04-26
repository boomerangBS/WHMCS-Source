<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\MarketConnect\Promotion\Service;

class Symantec extends AbstractService
{
    protected $name = \WHMCS\MarketConnect\MarketConnect::SERVICE_SYMANTEC;
    protected $friendlyName = "SSL";
    protected $primaryIcon;
    protected $supportsUpgrades = false;
    protected $qualifyingProductTypes;
    protected $productKeys;
    protected $sslTypes;
    protected $typesByBrand;
    protected $topHeroCertificatePriority;
    protected $certificateFeatures;
    protected $upsells;
    protected $defaultPromotionalContent;
    protected $promotionalContent;
    protected $upsellPromoContent;
    const ASSETS_FOLDER = "assets/img/marketconnect/symantec/";
    const SSL_RAPIDSSL = "rapidssl_rapidssl";
    const SSL_WILDCARD = "rapidssl_wildcard";
    const SSL_QUICKSSL = "geotrust_quickssl";
    const SSL_QUICKSSLPREMIUM = "geotrust_quicksslpremium";
    const SSL_TRUEBIZ = "geotrust_truebizid";
    const SSL_TRUEBIZEV = "geotrust_truebizidev";
    const SSL_QUICKSSLWILDCARD = "geotrust_quicksslpremiumwildcard";
    const SSL_TRUEBIZWILDCARD = "geotrust_truebizidwildcard";
    const SSL_SECURESITE = "digicert_securesite";
    const SSL_SECURESITEPRO = "digicert_securesitepro";
    const SSL_SECURESITEEV = "digicert_securesiteev";
    const SSL_SECURESITEPROEV = "digicert_securesiteproev";
    const SSL_TYPE_DV = "dv";
    const SSL_TYPE_EV = "ev";
    const SSL_TYPE_OV = "ov";
    const SSL_TYPE_WILDCARD = "wildcard";
    const SSL_TYPE_RAPIDSSL = "rapidssl";
    const SSL_TYPE_GEOTRUST = "geotrust";
    const SSL_TYPE_DIGICERT = "digicert";
    public function getSslTypes($byBrand = false)
    {
        return $byBrand ? $this->typesByBrand : $this->sslTypes;
    }
    public function getCertificateFeatures()
    {
        $returnData = $this->certificateFeatures;
        $langKey = "store.ssl.features.";
        $translatableStrings = ["displayName", "validation", "issuance", "for", "seal"];
        foreach ($returnData as $systemName => $attributes) {
            foreach ($attributes as $attribute => $value) {
                if(!in_array($attribute, $translatableStrings)) {
                } else {
                    $key = $langKey . $systemName . "." . $attribute;
                    $returnData[$systemName][$attribute] = $this->langStringOrFallback($key, $value);
                }
            }
        }
        return $returnData;
    }
    public function getCertificatesToDisplay($certificates) : array
    {
        $toDisplay = [];
        foreach ($this->topHeroCertificatePriority as $type => $products) {
            foreach ($products as $product) {
                if(empty($certificates[$product]) || !empty($toDisplay[$type]) || $this->isCertificateAddedToDisplay($certificates[$product], $toDisplay)) {
                } else {
                    $certificate = $certificates[$product];
                    $productKey = $certificate->getProductKeyAttribute();
                    $toDisplay[$type] = ["certificate" => $certificate, "features" => $this->translateFeatures($productKey), "description" => $this->translateDescription($productKey)];
                }
            }
        }
        return $toDisplay;
    }
    protected function isCertificateAddedToDisplay(\WHMCS\Product\Product $certificate, array $certificates) : \WHMCS\Product\Product
    {
        foreach ($certificates as $product) {
            if($product["certificate"]->id === $certificate->id) {
                return true;
            }
        }
        return false;
    }
    protected function translateDescription($productKey)
    {
        $description = $this->certificateFeatures[$productKey]["description"];
        $key = $this->getTranslationKey("description." . $description);
        return $this->langStringOrFallback($key, "");
    }
    protected function translateFeatures($productKey) : array
    {
        $features = $this->certificateFeatures[$productKey]["features"];
        $result = [];
        foreach ($features as $feature) {
            $result[] = $this->translateFeature($productKey, $feature);
        }
        return $result;
    }
    protected function translateFeature($productKey, string $featureKey)
    {
        $key = $this->getTranslationKey("feature." . $featureKey);
        switch ($featureKey) {
            case "warranty":
                return $this->langStringOrFallback($key, "", [":amount" => $this->certificateFeatures[$productKey][$featureKey]]);
                break;
            default:
                return $this->langStringOrFallback($key, "");
        }
    }
    protected function getTranslationKey($key)
    {
        return sprintf("store.ssl.landingPage.certificate.%s", $key);
    }
}

?>