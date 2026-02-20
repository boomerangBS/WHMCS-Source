<?php

namespace WHMCS\Service;

class Service extends \WHMCS\Model\AbstractModel implements \WHMCS\ServiceInterface, \WHMCS\Billing\Invoice\InvoicingServiceInterface, SubscriptionAwareInterface
{
    use \WHMCS\Domains\Traits\DomainTraits;
    use Traits\ProvisioningTraits;
    use Traits\SubscriptionAwareTrait;
    protected $table = "tblhosting";
    protected $columnMap = ["clientId" => "userid", "productId" => "packageid", "serverId" => "server", "registrationDate" => "regdate", "paymentGateway" => "paymentmethod", "status" => "domainstatus", "promotionId" => "promoid", "promotionCount" => "promocount", "overrideAutoSuspend" => "overideautosuspend", "overrideSuspendUntilDate" => "overidesuspenduntil", "bandwidthUsage" => "bwusage", "bandwidthLimit" => "bwlimit", "lastUpdateDate" => "lastupdate", "firstPaymentAmount" => "firstpaymentamount", "recurringAmount" => "amount", "recurringFee" => "amount", "subscriptionId" => "subscriptionid", "recommendationSourceProductId" => "recommendation_source_product_id"];
    protected $dates = ["registrationDate", "overrideSuspendUntilDate", "lastUpdateDate"];
    protected $booleans = ["overideautosuspend"];
    protected $appends = ["domainPunycode", "serviceProperties"];
    protected $hidden = ["password"];
    const STATUS_PENDING = \WHMCS\Utility\Status::PENDING;
    const STATUS_ACTIVE = \WHMCS\Utility\Status::ACTIVE;
    const STATUS_SUSPENDED = \WHMCS\Utility\Status::SUSPENDED;
    const STATUS_CANCELLED = \WHMCS\Utility\Status::CANCELLED;
    public static function boot()
    {
        parent::boot();
        static::observe("WHMCS\\Service\\Observers\\SslOrderServiceObserver");
        static::observe("WHMCS\\Service\\Observers\\ServiceHookObserver");
        static::observe("WHMCS\\Service\\Observers\\ServiceDataObserver");
    }
    public function getServiceActual() : \self
    {
        return $this;
    }
    public function getServiceSurrogate() : \self
    {
        return $this->parentalSibling ?? $this;
    }
    public function hasServiceSurrogate()
    {
        return $this->parentalSibling !== NULL;
    }
    public function getServiceClient() : \WHMCS\User\Client
    {
        return $this->client;
    }
    public function getServiceProperties() : Properties
    {
        return $this->serviceProperties;
    }
    public function scopeUserId(\Illuminate\Database\Eloquent\Builder $query, int $userId) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("userid", "=", $userId);
    }
    public function scopeDomain(\Illuminate\Database\Eloquent\Builder $query, string $domain) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("domain", "=", $domain);
    }
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("domainstatus", self::STATUS_ACTIVE);
    }
    public function scopeMarketConnect(\Illuminate\Database\Eloquent\Builder $query) : \Illuminate\Database\Eloquent\Builder
    {
        $marketConnectProductIds = \WHMCS\Product\Product::marketConnect()->pluck("id");
        return $query->whereIn("packageid", $marketConnectProductIds);
    }
    public function scopeIsConsideredActive(\Illuminate\Database\Eloquent\Builder $query) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereIn("domainstatus", [Service::STATUS_ACTIVE, Service::STATUS_SUSPENDED]);
    }
    public function scopeIsNotRecurring(\Illuminate\Database\Eloquent\Builder $query) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereIn("billingcycle", ["Free", "Free Account", "One Time"]);
    }
    public function isRecurring()
    {
        $cycle = (new \WHMCS\Billing\Cycles())->getNormalisedBillingCycle($this->billingcycle);
        return !in_array($cycle, [\WHMCS\Billing\Cycles::CYCLE_FREE, \WHMCS\Billing\Cycles::CYCLE_ONETIME]);
    }
    public function client()
    {
        return $this->belongsTo("WHMCS\\User\\Client", "userid", "id", "client");
    }
    public function product()
    {
        return $this->belongsTo("WHMCS\\Product\\Product", "packageid", "id", "product");
    }
    public function paymentGateway()
    {
        return $this->hasMany("WHMCS\\Module\\GatewaySetting", "gateway", "paymentmethod");
    }
    public function addons()
    {
        return $this->hasMany("WHMCS\\Service\\Addon", "hostingid");
    }
    public function order()
    {
        return $this->belongsTo("WHMCS\\Order\\Order", "orderid", "id", "order");
    }
    public function promotion()
    {
        return $this->hasMany("WHMCS\\Product\\Promotion", "id", "promoid");
    }
    public function cancellationRequests()
    {
        return $this->hasMany("WHMCS\\Service\\CancellationRequest", "relid");
    }
    public function ssl()
    {
        return $this->hasMany("WHMCS\\Service\\Ssl", "serviceid")->where("addon_id", "=", 0);
    }
    public function invoices() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany("WHMCS\\Billing\\Invoice", "tblinvoiceitems", "relid", "invoiceid", "id", "id", "invoices")->wherePivot("type", \WHMCS\Billing\Invoice\Item::TYPE_SERVICE);
    }
    public function hasAvailableUpgrades()
    {
        return 0 < $this->product->upgradeProducts->count();
    }
    public function failedActions()
    {
        return $this->hasMany("WHMCS\\Module\\Queue", "service_id")->where("service_type", "=", "service");
    }
    public function customFieldValues()
    {
        return $this->hasMany("WHMCS\\CustomField\\CustomFieldValue", "relid");
    }
    protected function getCustomFieldType()
    {
        return "product";
    }
    protected function getCustomFieldRelId()
    {
        return $this->product->id;
    }
    public function getServicePropertiesAttribute()
    {
        return new Properties($this);
    }
    public static function getNonTerminalStatuses() : array
    {
        return [self::STATUS_PENDING, self::STATUS_ACTIVE, self::STATUS_SUSPENDED];
    }
    public function canBeUpgraded()
    {
        return $this->status == self::STATUS_ACTIVE && $this->hasAvailableUpgrades() && !$this->hasOutstandingInvoices();
    }
    public function hasOutstandingInvoices()
    {
        foreach ($this->invoices as $invoice) {
            if($invoice->isAwaitingPayment() || $invoice->isUnpaid()) {
                return true;
            }
        }
        return false;
    }
    public function isService()
    {
        return true;
    }
    public function isAddon()
    {
        return false;
    }
    public function serverModel()
    {
        return $this->belongsTo("WHMCS\\Product\\Server", "server", "id", "serverModel");
    }
    public function legacyProvision()
    {
        try {
            if(!function_exists("ModuleCallFunction")) {
                require_once ROOTDIR . "/includes/modulefunctions.php";
            }
            return ModuleCallFunction("Create", $this->id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function getMetricProvider()
    {
        $server = $this->serverModel;
        if($server) {
            $metricProvider = $server->getMetricProvider();
            if($metricProvider) {
                return $metricProvider;
            }
        }
    }
    public function metrics($onlyBilledMetrics = false, $mode = NULL)
    {
        if(is_null($mode)) {
            $mode = \WHMCS\UsageBilling\Invoice\ServiceUsage::getQuickViewMode();
        }
        $serviceMetrics = [];
        $metricProvider = $this->getMetricProvider();
        if(!$metricProvider) {
            return $serviceMetrics;
        }
        $product = $this->product;
        $storedProductUsageItems = [];
        foreach ($product->metrics as $usageItem) {
            $storedProductUsageItems[$usageItem->metric] = $usageItem;
        }
        $usageTenant = $this->serverModel->usageTenantByService($this);
        foreach ($metricProvider->metrics() as $metric) {
            $currentUsage = NULL;
            $usageItem = NULL;
            $totalHistoricUsage = NULL;
            $totalHistoricSum = 0;
            $historicUsageByPeriod = [];
            $currentTenantStatId = NULL;
            if(isset($storedProductUsageItems[$metric->systemName()])) {
                $usageItem = $storedProductUsageItems[$metric->systemName()];
            }
            if($onlyBilledMetrics && !empty($usageItem->isHidden)) {
            } else {
                if($usageTenant) {
                    $stat = new \WHMCS\UsageBilling\Metrics\Server\Stat();
                    if($metric->type() == \WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_PERIOD_DAY) {
                        $startOfDayPeriod = \WHMCS\Carbon::now()->startOfDay();
                        $currentPeriodSum = 0;
                        $currentLastUpdated = \WHMCS\Carbon::now();
                        $currentPeriodStat = $stat->unbilledFirstAfter($startOfDayPeriod, $usageTenant, $metric);
                        if($currentPeriodStat) {
                            $currentLastUpdated = \WHMCS\Carbon::createFromTimestamp($currentPeriodStat->measuredAt);
                            $currentPeriodSum = $currentPeriodStat->value;
                            $currentTenantStatId = $currentPeriodStat->id;
                        }
                        $metric = $metric->withUsage(new \WHMCS\UsageBilling\Metrics\Usage($currentPeriodSum, $currentLastUpdated, $startOfDayPeriod->copy(), $startOfDayPeriod->copy()->endOfDayMicro()));
                        $previousDailyMetricPeriod = $startOfDayPeriod->copy()->subDay();
                        $historicUsageEnd = $previousDailyMetricPeriod->copy()->endOfDayMicro();
                        $historicUsageStart = $previousDailyMetricPeriod->copy()->startOfDayMicro();
                        $previousStats = $stat->unbilledQueryBefore($startOfDayPeriod, $usageTenant, $metric)->get();
                        foreach ($previousStats as $previous) {
                            $measured = \WHMCS\Carbon::createFromTimestamp($previous->measuredAt);
                            $start = $measured->copy()->startOfDayMicro();
                            $end = $measured->copy()->endOfDayMicro();
                            if($start < $historicUsageStart) {
                                $historicUsageStart = $start;
                            }
                            if($historicUsageEnd < $end) {
                                $historicUsageEnd = $end;
                            }
                            $historicUsageByPeriod[$previous->id] = new \WHMCS\UsageBilling\Metrics\Usage($previous->value, $measured->copy(), $start, $end);
                            $totalHistoricSum += $previous->value;
                        }
                        $totalHistoricUsage = new \WHMCS\UsageBilling\Metrics\Usage($totalHistoricSum, $historicUsageEnd, $historicUsageStart, $historicUsageEnd);
                    } elseif($metric->type() == \WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_PERIOD_MONTH) {
                        $startOfMetricPeriod = \WHMCS\Carbon::now()->startOfMonth();
                        $currentPeriodSum = 0;
                        $currentLastUpdated = \WHMCS\Carbon::now();
                        $currentPeriodStat = $stat->unbilledFirstAfter($startOfMetricPeriod, $usageTenant, $metric);
                        if($currentPeriodStat) {
                            $currentLastUpdated = \WHMCS\Carbon::createFromTimestamp($currentPeriodStat->measuredAt);
                            $currentPeriodSum = $currentPeriodStat->value;
                            $currentTenantStatId = $currentPeriodStat->id;
                        }
                        $metric = $metric->withUsage(new \WHMCS\UsageBilling\Metrics\Usage($currentPeriodSum, $currentLastUpdated, $startOfMetricPeriod->copy(), $startOfMetricPeriod->copy()->endOfMonthMicro()));
                        $previousMonthlyMetricPeriod = $startOfMetricPeriod->copy()->subMonth();
                        $historicUsageEnd = $previousMonthlyMetricPeriod->copy()->endOfMonthMicro();
                        $historicUsageStart = $previousMonthlyMetricPeriod->copy()->startOfMonthMicro();
                        $previousStats = $stat->unbilledQueryBefore($startOfMetricPeriod, $usageTenant, $metric)->get();
                        foreach ($previousStats as $previous) {
                            $measured = \WHMCS\Carbon::createFromTimestamp($previous->measuredAt);
                            $start = $measured->copy()->startOfMonthMicro();
                            $end = $measured->copy()->endOfMonthMicro();
                            if($start < $historicUsageStart) {
                                $historicUsageStart = $start;
                            }
                            if($historicUsageEnd < $end) {
                                $historicUsageEnd = $end;
                            }
                            $historicUsageByPeriod[$previous->id] = new \WHMCS\UsageBilling\Metrics\Usage($previous->value, $measured->copy(), $start, $end);
                            $totalHistoricSum += $previous->value;
                        }
                        $totalHistoricUsage = new \WHMCS\UsageBilling\Metrics\Usage($totalHistoricSum, $historicUsageEnd, $historicUsageStart, $historicUsageEnd);
                    } elseif($metric->type() == \WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_SNAPSHOT) {
                        $currentValue = 0;
                        $currentLastUpdated = NULL;
                        $currentPeriodStat = $stat->unbilledValueFirst($usageTenant, $metric);
                        if($currentPeriodStat) {
                            $currentLastUpdated = \WHMCS\Carbon::createFromTimestamp($currentPeriodStat->measuredAt);
                            $currentValue = $currentPeriodStat->value;
                            $currentTenantStatId = $currentPeriodStat->id;
                        }
                        $nextinvoicedate = $this->nextInvoiceDate;
                        if($nextinvoicedate != "0000-00-00") {
                            $nextinvoicedate = \WHMCS\Carbon::createFromFormat("Y-m-d", $nextinvoicedate);
                        } else {
                            $nextinvoicedate = \WHMCS\Carbon::now();
                        }
                        $nextinvoicedate->startOfDay();
                        $periodStart = $nextinvoicedate->copy()->subMonthNoOverflow();
                        if(!is_null($currentLastUpdated)) {
                            $metric = $metric->withUsage(new \WHMCS\UsageBilling\Metrics\Usage($currentValue, $currentLastUpdated, $periodStart, $nextinvoicedate));
                        } else {
                            $usage = new \WHMCS\UsageBilling\Metrics\NoUsage();
                            $metric = $metric->withUsage($usage);
                        }
                    }
                }
                if(\WHMCS\UsageBilling\Invoice\ServiceUsage::isMultiHistory($mode) && $historicUsageByPeriod) {
                    if(\WHMCS\UsageBilling\Invoice\ServiceUsage::isAllUsage($mode)) {
                        $serviceMetrics[] = \WHMCS\UsageBilling\Service\ServiceMetric::factoryFromMetric($this, $metric, NULL, $usageItem, $currentTenantStatId);
                    }
                    foreach ($historicUsageByPeriod as $tenantStatId => $usage) {
                        $serviceMetrics[] = \WHMCS\UsageBilling\Service\ServiceMetric::factoryFromMetric($this, $metric->withUsage($usage), NULL, $usageItem, $tenantStatId);
                    }
                } else {
                    $serviceMetrics[] = \WHMCS\UsageBilling\Service\ServiceMetric::factoryFromMetric($this, $metric, $totalHistoricUsage, $usageItem, $currentTenantStatId);
                }
            }
        }
        return $serviceMetrics;
    }
    public function getLink()
    {
        return \App::get_admin_folder_name() . "/clientsservices.php?productselect=" . $this->id;
    }
    public function getUniqueIdentifierValue($uniqueIdField)
    {
        $uniqueIdValue = NULL;
        if(!$uniqueIdField) {
            $uniqueIdField = "domain";
        }
        if(substr($uniqueIdField, 0, 12) == "customfield.") {
            $customFieldName = substr($uniqueIdField, 12);
            $uniqueIdValue = $this->serviceProperties->get($customFieldName);
        } else {
            $uniqueIdValue = $this->getAttribute($uniqueIdField);
        }
        return $uniqueIdValue;
    }
    public function getHexColorFromStatus()
    {
        switch ($this->status) {
            case \WHMCS\Utility\Status::PENDING:
                return "#FFFFCC";
                break;
            case \WHMCS\Utility\Status::SUSPENDED:
                return "#CCFF99";
                break;
            case \WHMCS\Utility\Status::TERMINATED:
            case \WHMCS\Utility\Status::CANCELLED:
            case \WHMCS\Utility\Status::FRAUD:
                return "#FF9999";
                break;
            case \WHMCS\Utility\Status::COMPLETED:
                return "#CCC";
                break;
            default:
                return "#FFF";
        }
    }
    public function getParentalSiblingAttribute() : \self
    {
        return \WHMCS\MarketConnect\Provision::findRelatedHostingService($this);
    }
    public function getProvisioningModuleName()
    {
        return $this->product != NULL ? $this->product->module : "";
    }
    public function getCustomActionData() : array
    {
        $data = [];
        $serverObj = new \WHMCS\Module\Server();
        $serverObj->loadByServiceID($this->id);
        if($serverObj->functionExists("CustomActions")) {
            $customActionCollection = $serverObj->call("CustomActions", $serverObj->getServerParams($this->serverModel));
            $userPermissions = (new \WHMCS\Authentication\CurrentUser())->client()->pivot->getPermissions();
            foreach ($customActionCollection as $customAction) {
                foreach ($customAction->getPermissions() as $requiredPermission) {
                    if(!$userPermissions->hasPermission($requiredPermission)) {
                    }
                }
                $data[] = ["identifier" => $customAction->getIdentifier(), "display" => \Lang::trans($customAction->getDisplay()) ?? $customAction->getDisplay(), "serviceid" => $this->id, "active" => $customAction->isActive(), "allowSamePage" => $customAction->isAllowSamePage(), "prefersIsolation" => $customAction->isPreferIsolation()];
            }
        }
        return $data;
    }
    public function getServiceProduct() : \WHMCS\Product\Product
    {
        return $this->product;
    }
    public function getInvoicingServiceItemType()
    {
        return \WHMCS\Billing\InvoiceItemInterface::TYPE_SERVICE;
    }
    public function getInvoicingServiceFirstPaymentAmount() : \WHMCS\View\Formatter\Price
    {
        return new \WHMCS\View\Formatter\Price($this->firstPaymentAmount, $this->getServiceClient()->currencyrel);
    }
    public function getInvoicingServiceRecurringAmount() : \WHMCS\View\Formatter\Price
    {
        return new \WHMCS\View\Formatter\Price($this->recurringAmount, $this->getServiceClient()->currencyrel);
    }
    public function isServiceMetricUsage()
    {
        $enabledMetrics = $this->product->enabledMetrics;
        if(is_null($enabledMetrics)) {
            return false;
        }
        return 0 < $enabledMetrics->count();
    }
    public function data()
    {
        return $this->hasMany("WHMCS\\Service\\ServiceData", "service_id");
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