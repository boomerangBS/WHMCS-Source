<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

class Promotion extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblpromotions";
    public $timestamps = false;
    protected $commaSeparated = ["appliesTo"];
    const TYPE_PERCENTAGE = "Percentage";
    const TYPE_FREE_SETUP = "Free Setup";
    const TYPE_PRICE_OVERRIDE = "Price Override";
    const TYPE_FIXED_AMOUNT = "Fixed Amount";
    public static function getApplicableToObject($object) : array
    {
        $allowAnyCode = checkPermission("Use Any Promotion Code on Order", true);
        $promos = self::all()->sortBy("code", SORT_NATURAL);
        $result = ["promos.activepromos" => [], "promos.expiredpromos" => [], "promos.allpromos" => []];
        if($object instanceof \WHMCS\Service\Addon) {
            $lookupTarget = $object->productAddon->id;
            $preferredLookupMethod = "appliesToAddon";
        } elseif($object instanceof \WHMCS\Domain\Domain) {
            $lookupTarget = $object->tld;
            $preferredLookupMethod = "appliesToDomain";
        } elseif($object instanceof \WHMCS\Service\Service) {
            $lookupTarget = $object->product->id;
            $preferredLookupMethod = "appliesToService";
        } else {
            return $result;
        }
        foreach ($promos as $promo) {
            if(!$allowAnyCode && !$promo->{$preferredLookupMethod}($lookupTarget)) {
            } elseif($promo->{$preferredLookupMethod}($lookupTarget)) {
                if(!$promo->isExpired()) {
                    $result["promos.activepromos"][$promo->id] = $promo;
                } else {
                    $result["promos.expiredpromos"][$promo->id] = $promo;
                }
            } else {
                $result["promos.allpromos"][$promo->id] = $promo;
            }
        }
        return $result;
    }
    public static function getAllForSelect() : array
    {
        $promos = self::all()->sortBy("code", SORT_NATURAL);
        $result = ["promos.activepromos" => [], "promos.expiredpromos" => [], "promos.allpromos" => []];
        foreach ($promos as $promo) {
            if(!$promo->isExpired()) {
                $result["promos.activepromos"][$promo->id] = $promo;
            } else {
                $result["promos.expiredpromos"][$promo->id] = $promo;
            }
        }
        return $result;
    }
    public function appliesToService($serviceId) : int
    {
        return in_array($serviceId, $this->appliesTo);
    }
    public function appliesToAddon($addonId) : int
    {
        return in_array("A" . $addonId, $this->appliesTo);
    }
    public function appliesToDomain($domainTld)
    {
        return in_array("D" . $domainTld, $this->appliesTo);
    }
    public function isExpired()
    {
        if($this->expirationdate === "0000-00-00") {
            return false;
        }
        try {
            $expiry = \WHMCS\Carbon::createFromFormat("Y-m-d", $this->expirationdate);
            if(!$expiry && !$expiry->isPast() && 0 < $this->maxuses && $this->uses < $this->maxuses) {
                return false;
            }
        } catch (\Exception $e) {
        }
        return true;
    }
    public function isRecurring()
    {
        return (bool) $this->recurring;
    }
    public function scopeByCode($query, string $code) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("code", $code);
    }
}

?>