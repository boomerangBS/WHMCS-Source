<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service;

class Addon extends \WHMCS\Model\AbstractModel implements \WHMCS\ServiceInterface, \WHMCS\Billing\Invoice\InvoicingServiceInterface, SubscriptionAwareInterface
{
    use \WHMCS\Domains\Traits\DomainTraits;
    use Traits\ProvisioningTraits;
    use Traits\SubscriptionAwareTrait;
    protected $table = "tblhostingaddons";
    protected $columnMap = ["orderId" => "orderid", "serviceId" => "hostingid", "clientId" => "userid", "recurringFee" => "recurring", "registrationDate" => "regdate", "prorataDate" => "proratadate", "applyTax" => "tax", "terminationDate" => "termination_date", "paymentGateway" => "paymentmethod", "serverId" => "server", "productId" => "addonid", "subscriptionId" => "subscriptionid", "firstPaymentAmount" => "firstpaymentamount"];
    protected $dates = ["regDate", "registrationDate", "nextdueDate", "nextinvoiceDate", "terminationDate", "prorataDate"];
    protected $appends = ["domainPunycode", "serviceProperties", "provisioningType"];
    public function getServiceActual() : Service
    {
        return $this->service;
    }
    public function getServiceSurrogate() : Service
    {
        return $this->getServiceActual();
    }
    public function hasServiceSurrogate()
    {
        return false;
    }
    public function getServiceClient() : \WHMCS\User\Client
    {
        return $this->client;
    }
    public function getServiceProperties() : Properties
    {
        return $this->serviceProperties;
    }
    public static function boot()
    {
        parent::boot();
        static::observe("WHMCS\\Service\\Observers\\SslOrderAddonObserver");
        static::observe("WHMCS\\Service\\Observers\\ServiceAddonDataObserver");
    }
    public function scopeUserId(\Illuminate\Database\Eloquent\Builder $query, $userId)
    {
        return $query->where("userid", "=", $userId);
    }
    public function scopeOfService(\Illuminate\Database\Eloquent\Builder $query, $serviceId)
    {
        return $query->where("hostingid", $serviceId);
    }
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where("status", Service::STATUS_ACTIVE);
    }
    public function scopeMarketConnect(\Illuminate\Database\Eloquent\Builder $query)
    {
        $marketConnectAddonIds = \WHMCS\Product\Addon::marketConnect()->pluck("id");
        return $query->whereIn("addonid", $marketConnectAddonIds);
    }
    public function scopeIsConsideredActive(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereIn("status", [Service::STATUS_ACTIVE, Service::STATUS_SUSPENDED]);
    }
    public function scopeIsNotRecurring($query)
    {
        return $query->whereIn("billingcycle", ["Free", "Free Account", "One Time"]);
    }
    public function service()
    {
        return $this->belongsTo("WHMCS\\Service\\Service", "hostingid", "id", "service");
    }
    public function productAddon()
    {
        return $this->belongsTo("WHMCS\\Product\\Addon", "addonid", "id", "productAddon");
    }
    public function client()
    {
        return $this->belongsTo("WHMCS\\User\\Client", "userid", "id", "client");
    }
    public function customFieldValues()
    {
        return $this->hasMany("WHMCS\\CustomField\\CustomFieldValue", "relid");
    }
    protected function getCustomFieldType()
    {
        return "addon";
    }
    protected function getCustomFieldRelId()
    {
        return $this->addonId;
    }
    public static function getNonTerminalStatuses() : array
    {
        return [\WHMCS\Utility\Status::PENDING, \WHMCS\Utility\Status::ACTIVE, \WHMCS\Utility\Status::SUSPENDED];
    }
    public function order()
    {
        return $this->belongsTo("WHMCS\\Order\\Order", "orderid", "id", "order");
    }
    public function paymentGateway()
    {
        return $this->hasMany("WHMCS\\Module\\GatewaySetting", "gateway", "paymentmethod");
    }
    public function getServicePropertiesAttribute()
    {
        return new Properties($this);
    }
    public function ssl()
    {
        return $this->hasMany("WHMCS\\Service\\Ssl", "addon_id", "id");
    }
    public function invoices() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany("WHMCS\\Billing\\Invoice", "tblinvoiceitems", "relid", "invoiceid", "id", "id", "invoices")->wherePivot("type", \WHMCS\Billing\Invoice\Item::TYPE_SERVICE_ADDON);
    }
    public function canBeUpgraded()
    {
        return $this->status == "Active";
    }
    public function isService()
    {
        return false;
    }
    public function isAddon()
    {
        return true;
    }
    public function serverModel()
    {
        return $this->hasOne("\\WHMCS\\Product\\Server", "id", "server");
    }
    public function failedActions()
    {
        return $this->hasMany("WHMCS\\Module\\Queue", "service_id")->where("service_type", "=", "addon");
    }
    public function moduleConfiguration()
    {
        return $this->hasMany("WHMCS\\Config\\Module\\ModuleConfiguration", "entity_id", "addonid")->where("entity_type", "=", "addon");
    }
    public function legacyProvision()
    {
        try {
            if(!function_exists("ModuleCallFunction")) {
                require_once ROOTDIR . "/includes/modulefunctions.php";
            }
            return ModuleCallFunction("Create", $this->serviceId, [], $this->id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function isProrated()
    {
        $parentService = $this->service;
        return $this->productAddon->prorate && $this->isRecurring() && $parentService->isRecurring() && $parentService->product->proRataBilling;
    }
    public function isRecurring()
    {
        return !in_array((new \WHMCS\Billing\Cycles())->getNormalisedBillingCycle($this->billingcycle), [\WHMCS\Billing\Cycles::CYCLE_FREE, \WHMCS\Billing\Cycles::CYCLE_ONETIME]);
    }
    public function isFree()
    {
        return (new \WHMCS\Billing\Cycles())->isFree($this->billingcycle);
    }
    public function getLink()
    {
        return \App::get_admin_folder_name() . "/clientsservices.php?productselect=a" . $this->id;
    }
    public function recalculateRecurringPrice()
    {
        try {
            if(!$this->addonId) {
                throw new \InvalidArgumentException();
            }
            $this->loadMissing(["productAddon", "service", "service.client"]);
            $pricing = $this->productAddon->pricing($this->service->client->currencyrel->toArray());
            $price = $pricing->byCycle($this->billingCycle);
            if($price instanceof \WHMCS\Product\Pricing\Price) {
                $price = $price->price()->getValue();
            }
            if(valueIsZero($price) || $price < 0) {
                $price = 0;
            }
            return $price * $this->qty;
        } catch (\Throwable $t) {
            return $this->recurringFee;
        }
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
    public function getServiceProduct() : \WHMCS\Product\AddonInterface
    {
        if($this->addonId == 0 || is_null($this->productAddon)) {
            return \WHMCS\Product\AdHocAddon::factory($this);
        }
        return $this->productAddon;
    }
    public function getInvoicingServiceItemType()
    {
        return \WHMCS\Billing\InvoiceItemInterface::TYPE_SERVICE_ADDON;
    }
    public function getInvoicingServiceFirstPaymentAmount() : \WHMCS\View\Formatter\Price
    {
        return new \WHMCS\View\Formatter\Price($this->firstPaymentAmount, $this->getServiceClient()->currencyrel);
    }
    public function getInvoicingServiceRecurringAmount() : \WHMCS\View\Formatter\Price
    {
        return new \WHMCS\View\Formatter\Price($this->recurringFee, $this->getServiceClient()->currencyrel);
    }
    public function isServiceMetricUsage()
    {
        return false;
    }
    private function overseeSubscriptionIdentifier($value)
    {
        if(0 < count(func_get_args())) {
            $this->subscriptionid = $value;
        }
        return $this->subscriptionid ?? "";
    }
    private function overseePaymentGatewayIdentifier($value)
    {
        if(0 < count(func_get_args())) {
            $this->paymentmethod = $value;
        }
        return $this->paymentmethod ?? "";
    }
}

?>