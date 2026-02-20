<?php

namespace WHMCS\Service;

class ServiceOnDemandRenewal implements ServiceOnDemandRenewalInterface
{
    protected $service;
    protected $serviceModel;
    protected $renewable;
    protected $reason = "";
    const ON_DEMAND_RENEWAL_STATS = "OnDemandRenewalStats";
    public function __construct(\WHMCS\ServiceInterface $service)
    {
        $this->service = $service;
    }
    public function getReason()
    {
        return $this->reason;
    }
    public function isRenewable()
    {
        if(is_null($this->renewable)) {
            $this->determineRenewalEligibility();
        }
        return $this->renewable;
    }
    public function determineRenewalEligibility() : \self
    {
        if($this->getService()->isServiceMetricUsage()) {
            $this->renewable = false;
            $this->reason = \Lang::trans("renewService.statusInfo.metricUsage");
            return $this;
        }
        $productModel = $this->getProduct();
        if(is_null($productModel) || !$productModel->getOnDemandRenewalSettings()->isEnabled()) {
            $this->renewable = false;
            $this->reason = \Lang::trans("renewService.statusInfo.notSupported");
            return $this;
        }
        $serviceStatus = $this->getService()->status;
        if($serviceStatus !== "Active") {
            $this->renewable = false;
            $this->reason = \Lang::trans("renewService.statusInfo.serviceStatus", [":serviceStatus" => $serviceStatus]);
            return $this;
        }
        if(!$this->getService()->isRecurring()) {
            $this->renewable = false;
            $this->reason = \Lang::trans("renewService.statusInfo.nonRecurring");
            return $this;
        }
        $serviceUnpaidInvoiceCount = $this->getService()->invoices->where("status", \WHMCS\Billing\Invoice::STATUS_UNPAID)->count();
        if(0 < $serviceUnpaidInvoiceCount) {
            $this->renewable = false;
            $this->reason = \Lang::trans("renewService.statusInfo.unpaidInvoices", [":unpaidInvoiceCount" => $serviceUnpaidInvoiceCount]);
            return $this;
        }
        $onDemandPeriod = $productModel->getOnDemandRenewalSettings()->getPeriodByBillingCycle($this->getBillingCycle());
        $nextDueDate = $this->getServiceNextDueDate();
        $onDemandPeriodStartDate = $this->getAdjustedStartDate($nextDueDate, $onDemandPeriod, $this->getBillingCycle());
        if($onDemandPeriod == 0 || $nextDueDate->isPast() || !\WHMCS\Carbon::now()->betweenIncluded($onDemandPeriodStartDate, $nextDueDate)) {
            $this->renewable = false;
            $this->reason = \Lang::trans("renewService.statusInfo.outsideRenewal");
            return $this;
        }
        $this->renewable = true;
        return $this;
    }
    public function renew($amount, string $paymentMethod) : \WHMCS\Billing\Invoice\Item
    {
        cancelUnpaidUpgrade($this->getServiceId());
        $dueDate = \WHMCS\Carbon::now();
        $promotionalArray = getInvoiceProductPromo($amount, $this->getService()->promotionId, $this->getService()->getServiceClient()->id, $this->getServiceId());
        $hasPromotion = false;
        if(isset($promotionalArray["description"])) {
            $amount -= $promotionalArray["amount"];
            $hasPromotion = true;
        }
        $invoiceItem = $this->createInvoiceItem(\WHMCS\Billing\InvoiceItemInterface::TYPE_SERVICE, $this->transformInvoiceItemDescription(), $amount, $dueDate, $paymentMethod);
        $invoiceItem->save();
        if($hasPromotion) {
            $this->createInvoiceItem(\WHMCS\Billing\InvoiceItemInterface::TYPE_HOSTING_PROMOTION, $promotionalArray["description"], $promotionalArray["amount"], $dueDate, $paymentMethod)->save();
        }
        self::trackRenewalCheckedOut("services");
        return $invoiceItem;
    }
    public function getBillingCycle()
    {
        return $this->getService()->billingCycle;
    }
    public function getPrice() : \WHMCS\View\Formatter\Price
    {
        return $this->getService()->getInvoicingServiceRecurringAmount();
    }
    public static function factoryByServiceId($serviceId) : \self
    {
        $service = Service::find($serviceId);
        if(is_null($service)) {
            return NULL;
        }
        return self::factoryByService($service);
    }
    public static function factoryByService(\WHMCS\ServiceInterface $service) : \self
    {
        return new self($service);
    }
    public static function getEligibleServiceOnDemandRenewals(\WHMCS\User\Client $client) : \Illuminate\Support\Collection
    {
        return Service::userId($client->id)->with(["client", "product", "invoices", "product.overrideOnDemandRenewal", "product.enabledMetrics"])->active()->get()->map(function (\WHMCS\ServiceInterface $service) {
            return new ServiceOnDemandRenewal($service);
        })->filter(function (ServiceOnDemandRenewal $onDemandRenewal) use($client) {
            return self::filterIsRenewable($onDemandRenewal, $client);
        });
    }
    public static function getServiceOnDemandRenewals(\WHMCS\User\Client $client) : \Illuminate\Support\Collection
    {
        return Service::userId($client->id)->whereIn("domainstatus", [Status::PENDING, Status::ACTIVE, Status::SUSPENDED])->get()->map(function (\WHMCS\ServiceInterface $service) {
            return new ServiceOnDemandRenewal($service);
        });
    }
    public static function filterIsRenewable(ServiceOnDemandRenewal $onDemandService, $client) : ServiceOnDemandRenewal
    {
        if(is_null($onDemandService) || is_null($client) || !$onDemandService->canRenew($client)) {
            return NULL;
        }
        return $onDemandService;
    }
    public function isMyClient(\WHMCS\User\Client $client) : \WHMCS\User\Client
    {
        return $this->getService()->client->id == $client->id;
    }
    public function canRenew(\WHMCS\User\Client $client) : \WHMCS\User\Client
    {
        return $this->isRenewable() && $this->isMyClient($client);
    }
    public function getService()
    {
        return $this->service;
    }
    public function getProduct()
    {
        if(is_null($this->serviceModel)) {
            $this->serviceModel = $this->getService()->getServiceProduct();
        }
        return $this->serviceModel;
    }
    public function isTaxable()
    {
        return $this->getProduct()->applyTax;
    }
    public function getServiceId() : int
    {
        return $this->getService()->id;
    }
    public function getServiceNextDueDate() : \WHMCS\Carbon
    {
        return \WHMCS\Carbon::parse($this->getService()->nextDueDate);
    }
    public function getProductName()
    {
        return $this->getProduct()->name;
    }
    public function getNextPayUntilDate() : \Carbon\CarbonInterface
    {
        if(!function_exists("getInvoicePayUntilDate")) {
            require ROOTDIR . "/includes/invoicefunctions.php";
        }
        return \WHMCS\Carbon::safeCreateFromMySqlDate(getInvoicePayUntilDate($this->getService()->nextDueDate, $this->getBillingCycle()));
    }
    protected function getAdjustedStartDate(\Carbon\Carbon $nextDueDate, int $onDemandPeriod, string $billingCycle) : \Carbon\CarbonInterface
    {
        $onDemandPeriod = max($onDemandPeriod, 0);
        try {
            $months = (new \WHMCS\Billing\Cycles())->getNumberOfMonths($billingCycle);
        } catch (\Exception $e) {
            $months = 1;
        }
        return max($nextDueDate->copy()->subDays($onDemandPeriod), $nextDueDate->copy()->subMonths($months));
    }
    protected function createInvoiceItem($invoiceItemType, string $description, $amount, $dueDate, string $paymentMethod) : \WHMCS\Billing\Invoice\Item
    {
        $invoiceItem = new \WHMCS\Billing\Invoice\Item();
        $invoiceItem->userId = $this->getService()->getServiceClient()->id;
        $invoiceItem->type = $invoiceItemType;
        $invoiceItem->relatedEntityId = $this->getServiceId();
        $invoiceItem->description = $description;
        $invoiceItem->amount = $amount;
        $invoiceItem->taxed = $this->isTaxable();
        $invoiceItem->dueDate = $dueDate->toDateString();
        $invoiceItem->paymentMethod = $paymentMethod;
        return $invoiceItem;
    }
    protected function transformInvoiceItemDescription()
    {
        if(!function_exists("getInvoicePayUntilDate")) {
            require ROOTDIR . "/includes/invoicefunctions.php";
        }
        $serviceDetails = getInvoiceProductDetails($this->getServiceId(), $this->getProduct()->id, "", $this->getService()->nextDueDate, $this->getBillingCycle(), $this->getService()->getServiceDomain(), $this->getService()->getServiceClient()->id);
        $descriptionPrefix = \Lang::trans("renewService.titleAltSingular");
        return $descriptionPrefix . " - " . $serviceDetails["description"];
    }
    public static function trackRenewalAddedToCart()
    {
        self::trackRenewalAddedToCartByType("services");
    }
    public static function trackRenewalCheckedOut()
    {
        self::trackRenewalCheckedOutByType("services");
    }
    public static function trackRenewalAddedToCartByType(string $renewalType)
    {
        $settingValue = "";
        $setting = \WHMCS\Config\Setting::find(self::ON_DEMAND_RENEWAL_STATS);
        if(!is_null($setting)) {
            $settingValue = $setting->value;
        }
        $tracker = self::trackRenewals()->unpack($settingValue);
        $tracker->incrementAddedToCart($renewalType);
        \WHMCS\Config\Setting::setValue(self::ON_DEMAND_RENEWAL_STATS, $tracker->pack());
    }
    public static function trackRenewalCheckedOutByType(string $renewalType)
    {
        $settingValue = "";
        $setting = \WHMCS\Config\Setting::find(self::ON_DEMAND_RENEWAL_STATS);
        if(!is_null($setting)) {
            $settingValue = $setting->value;
        }
        $tracker = self::trackRenewals()->unpack($settingValue);
        $tracker->incrementCheckedOut($renewalType);
        \WHMCS\Config\Setting::setValue(self::ON_DEMAND_RENEWAL_STATS, $tracker->pack());
    }
    public static function trackRenewals()
    {
        return new func_num_args();
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F536572766963652F536572766963654F6E44656D616E6452656E6577616C2E7068703078376664353934323461346636_
{
    public $domains;
    public $services;
    public $addons;
    public function unpack(string $renewalTracking)
    {
        $data = json_decode($renewalTracking);
        if(!is_null($data) && json_last_error() === JSON_ERROR_NONE) {
            $unpacked = new self();
            $unpacked->domains = $data->domains;
            $unpacked->services = $data->services;
            $unpacked->addons = $data->addons;
            return $unpacked;
        }
        $this->domains = $this->createItem();
        $this->services = $this->createItem();
        $this->addons = $this->createItem();
        return $this;
    }
    public function incrementAddedToCart(string $renewalType)
    {
        $today = \WHMCS\Carbon::today();
        switch ($renewalType) {
            case "domains":
                $this->domains = $this->incrementItemAddedToCart($this->domains, $today);
                break;
            case "services":
                $this->services = $this->incrementItemAddedToCart($this->services, $today);
                break;
            case "addons":
                $this->addons = $this->incrementItemAddedToCart($this->addons, $today);
                break;
            default:
                throw new \Exception("Renewal Type is not supported.");
        }
    }
    public function incrementCheckedOut(string $renewalType)
    {
        $today = \WHMCS\Carbon::today();
        switch ($renewalType) {
            case "domains":
                $this->domains = $this->incrementItemCheckedOut($this->domains, $today);
                break;
            case "services":
                $this->services = $this->incrementItemCheckedOut($this->services, $today);
                break;
            case "addons":
                $this->addons = $this->incrementItemCheckedOut($this->addons, $today);
                break;
            default:
                throw new \Exception("Renewal Type is not supported.");
        }
    }
    public function discardDatesOlderThanDays(int $days)
    {
        $this->domains = $this->discardItemDatesAfterDays($this->domains, $days);
        $this->services = $this->discardItemDatesAfterDays($this->services, $days);
        $this->addons = $this->discardItemDatesAfterDays($this->addons, $days);
        return $this;
    }
    public function pack()
    {
        $objectClassProperties = [];
        foreach (get_class_vars(get_class($this)) as $property => $value) {
            $objectClassProperties[$property] = $this->{$property};
        }
        return json_encode((object) $objectClassProperties);
    }
    public function getTotalByRenewalType(string $renewalType)
    {
        switch ($renewalType) {
            case "domains":
                return $this->getItemDatesTotal($this->domains);
                break;
            case "services":
                return $this->getItemDatesTotal($this->services);
                break;
            case "addons":
                return $this->getItemDatesTotal($this->addons);
                break;
            default:
                throw new \Exception("Renewal Type is not supported.");
        }
    }
    public function toArray() : array
    {
        $domainTotal = $this->getTotalByRenewalType("domains");
        $serviceTotal = $this->getTotalByRenewalType("services");
        $addonTotal = $this->getTotalByRenewalType("addons");
        return ["domains" => ["lifeTimeAddedToCart" => $this->domains->lifeTimeAddedToCart, "lifeTimeCheckedOut" => $this->domains->lifeTimeCheckedOut, "thirtyDays" => ["addedToCart" => $domainTotal->addedToCart, "checkedOut" => $domainTotal->checkedOut]], "services" => ["lifeTimeAddedToCart" => $this->services->lifeTimeAddedToCart, "lifeTimeCheckedOut" => $this->services->lifeTimeCheckedOut, "thirtyDays" => ["addedToCart" => $serviceTotal->addedToCart, "checkedOut" => $serviceTotal->checkedOut]], "addons" => ["lifeTimeAddedToCart" => $this->addons->lifeTimeAddedToCart, "lifeTimeCheckedOut" => $this->addons->lifeTimeCheckedOut, "thirtyDays" => ["addedToCart" => $addonTotal->addedToCart, "checkedOut" => $addonTotal->checkedOut]]];
    }
    protected function createItem()
    {
        return new func_num_args();
    }
    protected function createItemTodayDate($item, \WHMCS\Carbon $date)
    {
        $newDateTracker = $this->createItemDate();
        $item->dates = (array) $item->dates;
        if(!isset($item->dates[(string) $date->toDateString()])) {
            $item->dates[(string) $date->toDateString()] = $newDateTracker;
        }
        return $item;
    }
    protected function createItemDate()
    {
        return new func_num_args();
    }
    protected function incrementItemAddedToCart($item, \WHMCS\Carbon $date)
    {
        $item->lifeTimeAddedToCart += 1;
        $item = $this->createItemTodayDate($item, $date);
        $item->dates[(string) $date->toDateString()]->addedToCart += 1;
        return $item;
    }
    protected function incrementItemCheckedOut($item, \WHMCS\Carbon $date)
    {
        $item->lifeTimeCheckedOut += 1;
        $item = $this->createItemTodayDate($item, $date);
        $item->dates[(string) $date->toDateString()]->checkedOut += 1;
        return $item;
    }
    protected function discardItemDatesAfterDays($item, int $days)
    {
        if(empty($item->dates)) {
            return $item;
        }
        $today = \WHMCS\Carbon::today()->startOfDay();
        $daysAgo = $today->clone()->subDays($days);
        $item->dates = (array) $item->dates;
        foreach ($item->dates as $date => $data) {
            $trackedDate = \WHMCS\Carbon::parse($date)->startOfDay();
            if(!$trackedDate->lessThanOrEqualTo($today) || !$trackedDate->greaterThanOrEqualTo($daysAgo)) {
                unset($item->dates[(string) $trackedDate->toDateString()]);
            }
        }
        return $item;
    }
    protected function getItemDatesTotal($item)
    {
        $result = $this->createItemDate();
        if(!isset($item->dates)) {
            return $result;
        }
        foreach ($item->dates as $dateData) {
            $result->addedToCart += $dateData->addedToCart;
            $result->checkedOut += $dateData->checkedOut;
        }
        return $result;
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F536572766963652F536572766963654F6E44656D616E6452656E6577616C2E7068703078376664353934323439613062_
{
    public $lifeTimeAddedToCart = 0;
    public $lifeTimeCheckedOut = 0;
    public $dates = [];
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F536572766963652F536572766963654F6E44656D616E6452656E6577616C2E7068703078376664353934323439633839_
{
    public $addedToCart = 0;
    public $checkedOut = 0;
}

?>