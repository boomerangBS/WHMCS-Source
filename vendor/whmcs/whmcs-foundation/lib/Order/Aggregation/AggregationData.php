<?php

namespace WHMCS\Order\Aggregation;

class AggregationData
{
    private $count = 0;
    private $totalAmount = 0;
    private $itemsTotalCount = 0;
    private $servicesTotalCount = 0;
    private $addonsTotalCount = 0;
    private $domainsTotalCount = 0;
    private $marketconnectItemsTotalCount = 0;
    private $totalPromoAmount = 0;
    private $servicesHostingOrdersCount = 0;
    private $servicesResellerOrdersCount = 0;
    private $servicesServerOrdersCount = 0;
    private $servicesOtherOrdersCount = 0;
    private $marketconnectItemOrdersCount = 0;
    private $serviceAddonOrdersCount = 0;
    private $serviceDomainOrdersCount = 0;
    private $serviceAddonDomainOrdersCount = 0;
    const AVERAGE_COUNT_PRECISION = 4;
    const MONEY_PRECISION = 2;
    const ORDERS_PERCENTAGE_PRECISION = 2;
    public function addItemToAggregation($orderStatisticItem) : \self
    {
        $this->count++;
        $this->totalAmount += $orderStatisticItem->default_currency_amount;
        $this->itemsTotalCount += $orderStatisticItem->items_count;
        $this->servicesTotalCount += $orderStatisticItem->services_count;
        $this->addonsTotalCount += $orderStatisticItem->addons_count;
        $this->domainsTotalCount += $orderStatisticItem->domains_count;
        $this->marketconnectItemsTotalCount += $orderStatisticItem->marketconnect_items_count + $orderStatisticItem->marketconnect_upgrade_items_count;
        $this->totalPromoAmount += (double) $orderStatisticItem->default_currency_promo_amount;
        if(0 < $orderStatisticItem->services_hosting_count) {
            $this->servicesHostingOrdersCount++;
        }
        if(0 < $orderStatisticItem->services_reseller_count) {
            $this->servicesResellerOrdersCount++;
        }
        if(0 < $orderStatisticItem->services_server_count) {
            $this->servicesServerOrdersCount++;
        }
        if(0 < $orderStatisticItem->services_other_count) {
            $this->servicesOtherOrdersCount++;
        }
        if(0 < $orderStatisticItem->marketconnect_items_count || 0 < $orderStatisticItem->marketconnect_upgrade_items_count) {
            $this->marketconnectItemOrdersCount++;
        }
        if(0 < $orderStatisticItem->services_count && 0 < $orderStatisticItem->addons_count) {
            $this->serviceAddonOrdersCount++;
        }
        if(0 < $orderStatisticItem->services_count && 0 < $orderStatisticItem->domains_count) {
            $this->serviceDomainOrdersCount++;
        }
        if(0 < $orderStatisticItem->services_count && 0 < $orderStatisticItem->addons_count && 0 < $orderStatisticItem->domains_count) {
            $this->serviceAddonDomainOrdersCount++;
        }
        return $this;
    }
    public function getCount() : int
    {
        return $this->count;
    }
    public function getItemsCountAverage()
    {
        return $this->calculateAverage($this->itemsTotalCount, self::AVERAGE_COUNT_PRECISION);
    }
    public function getPriceAverage()
    {
        return $this->calculateAverage($this->totalAmount, self::MONEY_PRECISION);
    }
    public function getServicesCountAverage()
    {
        return $this->calculateAverage($this->servicesTotalCount, self::AVERAGE_COUNT_PRECISION);
    }
    public function getAddonsCountAverage()
    {
        return $this->calculateAverage($this->addonsTotalCount, self::AVERAGE_COUNT_PRECISION);
    }
    public function getDomainsCountAverage()
    {
        return $this->calculateAverage($this->domainsTotalCount, self::AVERAGE_COUNT_PRECISION);
    }
    public function getMarketConnectItemsCountAverage()
    {
        return $this->calculateAverage($this->marketconnectItemsTotalCount, self::AVERAGE_COUNT_PRECISION);
    }
    public function getPromoAmountAverage()
    {
        return $this->calculateAverage(abs($this->totalPromoAmount), self::MONEY_PRECISION);
    }
    public function getHostingOrdersPercentage()
    {
        return $this->calculateOrdersPercentage($this->servicesHostingOrdersCount);
    }
    public function getResellerOrdersPercentage()
    {
        return $this->calculateOrdersPercentage($this->servicesResellerOrdersCount);
    }
    public function getServerOrdersPercentage()
    {
        return $this->calculateOrdersPercentage($this->servicesServerOrdersCount);
    }
    public function getOtherOrdersPercentage()
    {
        return $this->calculateOrdersPercentage($this->servicesOtherOrdersCount);
    }
    public function getMarketConnectItemOrdersPercentage()
    {
        return $this->calculateOrdersPercentage($this->marketconnectItemOrdersCount);
    }
    public function getServiceAddonOrdersPercentage()
    {
        return $this->calculateOrdersPercentage($this->serviceAddonOrdersCount);
    }
    public function getServiceDomainOrdersPercentage()
    {
        return $this->calculateOrdersPercentage($this->serviceDomainOrdersCount);
    }
    public function getServiceAddonDomainOrdersPercentage()
    {
        return $this->calculateOrdersPercentage($this->serviceAddonDomainOrdersCount);
    }
    private function calculateAverage($value, int $precision) : int
    {
        $average = $this->count === 0 ? 0 : $value / $this->count;
        return round($average, $precision);
    }
    private function calculateOrdersPercentage($value)
    {
        $average = $this->count === 0 ? 0 : $value / $this->count * 100;
        return round($average, self::ORDERS_PERCENTAGE_PRECISION);
    }
}

?>