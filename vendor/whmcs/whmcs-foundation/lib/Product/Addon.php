<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

class Addon extends \WHMCS\Model\AbstractModel implements PricedEntityInterface, AddonInterface
{
    use MarketConnectTrait;
    use OnDemandRenewalTrait;
    protected $table = "tbladdons";
    protected $moduleField = "module";
    protected $columnMap = ["applyTax" => "tax", "showOnOrderForm" => "showorder", "welcomeEmailTemplateId" => "welcomeemail", "allowMultipleQuantities" => "allowqty", "autoLinkCriteria" => "autolinkby", "isHidden" => "hidden", "isRetired" => "retired"];
    protected $booleans = ["applyTax", "showOnOrderForm", "suspendProduct", "isHidden", "retired"];
    protected $commaSeparated = ["packages", "downloads"];
    protected $casts = ["allowqty" => "integer", "autolinkby" => "array"];
    protected $pricingCache;
    protected $appends = ["provisioningType"];
    const PROVISIONING_TYPE_FEATURE = "feature";
    const PROVISIONING_TYPE_STANDARD = "standard";
    public static function boot()
    {
        if(!function_exists("logAdminActivity")) {
            require_once ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "adminfunctions.php";
        }
        parent::boot();
        self::observe(["WHMCS\\Product\\Observers\\AddonObserver", "WHMCS\\Product\\Observers\\AddonOnDemandRenewalObserver"]);
        static::addGlobalScope("ordered", function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->orderBy("tbladdons.weight")->orderBy("tbladdons.name");
        });
    }
    public function scopeShowOnOrderForm(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where("showorder", "=", 1);
    }
    public function scopeIsHidden(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where("hidden", "=", 1);
    }
    public function scopeIsNotHidden(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where("hidden", "=", 0);
    }
    public function scopeIsRetired(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where("retired", "=", 1);
    }
    public function scopeIsNotRetired(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where("retired", "=", 0);
    }
    public function scopeAvailableOnOrderForm(\Illuminate\Database\Eloquent\Builder $query, array $addons = [])
    {
        $query->where(function (\Illuminate\Database\Eloquent\Builder $query) {
            $query->where("showorder", 1)->where("retired", 0);
            if(defined("CLIENTAREA")) {
                $query->where("hidden", 0);
            }
        });
        if(0 < count($addons)) {
            $query->orWhere(function (\Illuminate\Database\Eloquent\Builder $query) use($addons) {
                $query->where("showorder", 1)->where("retired", 0)->whereIn("id", $addons);
            });
        }
        return $query;
    }
    public function scopeSorted($query)
    {
        return $query->orderBy("weight");
    }
    public function welcomeEmailTemplate()
    {
        return $this->hasOne("WHMCS\\Mail\\Template", "id", "welcomeemail");
    }
    public function getNameAttribute($name)
    {
        $translatedName = "";
        if(\WHMCS\Config\Setting::getValue("EnableTranslations")) {
            $translatedName = \Lang::trans("product_addon." . $this->id . ".name", [], "dynamicMessages");
        }
        return strlen($translatedName) && $translatedName != "product_addon." . $this->id . ".name" ? $translatedName : $name;
    }
    public function getDescriptionAttribute($description)
    {
        $translatedDescription = "";
        if(\WHMCS\Config\Setting::getValue("EnableTranslations")) {
            $translatedDescription = \Lang::trans("product_addon." . $this->id . ".description", [], "dynamicMessages");
        }
        return strlen($translatedDescription) && $translatedDescription != "product_addon." . $this->id . ".description" ? $translatedDescription : $description;
    }
    public function customFields()
    {
        return $this->hasMany("WHMCS\\CustomField", "relid")->where("type", "=", "addon")->orderBy("sortorder");
    }
    public function serviceAddons()
    {
        return $this->hasMany("WHMCS\\Service\\Addon", "addonid");
    }
    public function dbPricing() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany("WHMCS\\Billing\\Addon\\Pricing", "relid");
    }
    public function moduleConfiguration()
    {
        return $this->hasMany("WHMCS\\Config\\Module\\ModuleConfiguration", "entity_id")->where("entity_type", "=", "addon");
    }
    public function translatedNames()
    {
        return $this->hasMany("WHMCS\\Language\\DynamicTranslation", "related_id")->where("related_type", "=", "product_addon.{id}.name")->select(["language", "translation"]);
    }
    public function translatedDescriptions()
    {
        return $this->hasMany("WHMCS\\Language\\DynamicTranslation", "related_id")->where("related_type", "=", "product_addon.{id}.description")->select(["language", "translation"]);
    }
    public static function getAddonName($addonId, $fallback = "", $language = NULL)
    {
        $name = \Lang::trans("product_addon." . $addonId . ".name", [], "dynamicMessages", $language);
        if($name == "product_addon." . $addonId . ".name") {
            if($fallback) {
                return $fallback;
            }
            return Addon::find($addonId, ["name"])->name;
        }
        return $name;
    }
    public static function getAddonDescription($addonId, $fallback = "", $language = NULL)
    {
        $description = \Lang::trans("product_addon." . $addonId . ".description", [], "dynamicMessages", $language);
        if($description == "product_addon." . $addonId . ".description") {
            if($fallback) {
                return $fallback;
            }
            return Product::find($addonId, ["description"])->description;
        }
        return $description;
    }
    public function pricing($currency) : Pricing
    {
        if(is_null($this->pricingCache)) {
            $this->pricingCache = new Pricing($this, $currency);
        }
        return $this->pricingCache;
    }
    public function isFree()
    {
        return $this->billingCycle == "free";
    }
    public function isOneTime()
    {
        return $this->billingCycle == "onetime";
    }
    public function scopeMarketConnect(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where("module", "marketconnect");
    }
    public function getProductKeyAttribute($value)
    {
        return $this->moduleConfiguration()->where("setting_name", "configoption1")->first()->value;
    }
    public function isMarketConnectAddon()
    {
        return $this->module == "marketconnect";
    }
    public function getServiceKeyAttribute($value)
    {
        $productKey = $this->productKey;
        $parts = explode("_", $productKey, 2);
        return !empty($parts[0]) ? $parts[0] : NULL;
    }
    public function isValidForUpgrade(Addon $addon)
    {
        if($this->isMarketConnectAddon() && !empty($addon->serviceKey) && $this->serviceKey == $addon->serviceKey) {
            return true;
        }
        return false;
    }
    public function isVisibleOnOrderForm(array $addonIds = [])
    {
        $inClientArea = defined("CLIENTAREA");
        $inAdminArea = defined("ADMINAREA");
        if(!$this->retired && $this->showOnOrderForm || $inAdminArea || $inClientArea && (!$this->isHidden || !in_array($this->id, $addonIds))) {
            return true;
        }
        return false;
    }
    public static function getAddonDropdownValues($currentAddonId) : array
    {
        $addonCollection = self::all();
        $dropdownOptions = [];
        foreach ($addonCollection as $addon) {
            if($addon->retired && $currentAddonId != $addon->id) {
            } else {
                $dropdownOptions[$addon->id] = ["text" => $addon->name, "dataAttributes" => ["allowMultiple" => $addon->allowMultipleQuantities]];
            }
        }
        return $dropdownOptions;
    }
    public function emailMarketerRules()
    {
        return $this->belongsToMany("WHMCS\\Admin\\Utilities\\Tools\\EmailMarketer", "tblemailmarketer_related_pivot", "addon_id", "task_id", "id", "id", "emailMarketerRules")->withTimestamps();
    }
    public function getAvailableBillingCycles() : array
    {
        switch ($this->billingCycle) {
            case "free":
                return ["free"];
                break;
            case "onetime":
                return ["onetime"];
                break;
            case "recurring":
                $validCycles = [];
                $productPricing = new \WHMCS\Pricing();
                $productPricing->loadPricing("addon", $this->id);
                return $productPricing->getAvailableBillingCycles();
                break;
            default:
                return [];
        }
    }
    public function duplicate($newAddonName) : Addon
    {
        $newAddon = $this->replicate();
        $newAddon->name = $newAddonName;
        $newAddon->save();
        \WHMCS\Billing\Pricing::where(["type" => "addon", "relid" => $this->id])->each(function ($pricing) use($newAddon) {
            $pricing->replicate()->fill(["relid" => $newAddon->id])->save();
        });
        $this->customFields->each(function ($customField) use($newAddon) {
            $customField->replicate()->fill(["relid" => $newAddon->id])->save();
        });
        return $this->duplicateOverrideOnDemandRenewal($newAddon);
    }
    public function getProvisioningTypeAttribute()
    {
        $provisioningType = "standard";
        $moduleConfiguration = $this->moduleConfiguration()->where("setting_name", "provisioningType")->first();
        if($moduleConfiguration) {
            $provisioningType = $moduleConfiguration->value;
        }
        return $provisioningType;
    }
    public function getOrderLineItemProductGroupName()
    {
        return "Addons";
    }
    public function overrideOnDemandRenewal() : \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne("WHMCS\\Product\\OnDemandRenewal", "rel_id")->where("rel_type", "=", OnDemandRenewal::ON_DEMAND_RENEWAL_TYPE_ADDON);
    }
    public function overridingOnDemandRenewal($enable, int $monthly, int $quarterly, int $semiannually, int $annually, int $biennially, int $triennially) : \self
    {
        if(is_null($this->overrideOnDemandRenewal)) {
            OnDemandRenewal::findOrCreate(OnDemandRenewal::ON_DEMAND_RENEWAL_TYPE_ADDON, $this->id);
            $this->refresh();
        }
        return $this->doOverridingOnDemandRenewal($enable, $monthly, $quarterly, $semiannually, $annually, $biennially, $triennially);
    }
}

?>