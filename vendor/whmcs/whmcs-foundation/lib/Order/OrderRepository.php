<?php

namespace WHMCS\Order;

class OrderRepository
{
    private $currencyRepository;
    public function __construct(\WHMCS\Product\CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }
    public function getLast30DaysPaidOrdersStatistics() : array
    {
        try {
            return $this->getPaidOrderStatistics(30);
        } catch (\Throwable $e) {
            return [];
        }
    }
    public function getLast90DaysPaidOrdersStatistics() : array
    {
        try {
            return $this->getPaidOrderStatistics(90);
        } catch (\Throwable $e) {
            return [];
        }
    }
    private function getPaidOrderStatistics($lastDays) : array
    {
        $ordersStatistics = $this->getOrdersStatisticsQueryBuilder($lastDays)->toBase()->cursor();
        $ordersAggregationRules = $this->getOrdersAggregationCriteria();
        $overriddenAggregationCalculators = $this->getOverriddenAggregationCalculators();
        $ordersAggregations = [];
        foreach ($ordersAggregationRules as $aggregationName => $aggregationRuleCallback) {
            $ordersAggregations[$aggregationName] = new Aggregation\AggregationData();
        }
        foreach ($ordersStatistics as $orderStatisticItem) {
            $orderStatisticItem->items_count = $orderStatisticItem->services_count + $orderStatisticItem->addons_count + $orderStatisticItem->domains_count + $orderStatisticItem->domain_renewals_count + $orderStatisticItem->upgrades_count;
            $orderStatisticItem->marketconnect_items_count = $orderStatisticItem->marketconnect_services_count + $orderStatisticItem->marketconnect_addons_count;
            $orderStatisticItem->marketconnect_upgrade_items_count = 0 < $orderStatisticItem->marketconnect_service_upgrades_count + $orderStatisticItem->marketconnect_addon_upgrades_count;
            foreach ($ordersAggregationRules as $aggregationName => $aggregationRuleCallback) {
                if(empty($ordersAggregations[$aggregationName])) {
                    $ordersAggregations[$aggregationName] = new Aggregation\AggregationData();
                }
                if($aggregationRuleCallback($orderStatisticItem)) {
                    $ordersAggregations[$aggregationName]->addItemToAggregation($orderStatisticItem);
                }
            }
        }
        $calculatedStatisticsPerAggregation = [];
        foreach ($ordersAggregations as $aggregationName => $aggregationData) {
            if(!empty($overriddenAggregationCalculators[$aggregationName])) {
                $calculatedStatisticsPerAggregation[$aggregationName] = call_user_func($overriddenAggregationCalculators[$aggregationName], $aggregationData);
            } else {
                $calculatedStatisticsPerAggregation[$aggregationName] = $this->calculateAggregationStatistics($aggregationData);
            }
        }
        return ["total" => ["admin" => $calculatedStatisticsPerAggregation["total_admin_placed"], "customer_portal" => $calculatedStatisticsPerAggregation["total_customer_portal_placed"], "admin_masquerading_placed" => $calculatedStatisticsPerAggregation["total_admin_masquerading_placed"], "client_api" => $calculatedStatisticsPerAggregation["total_client_api_placed"], "local_api" => $calculatedStatisticsPerAggregation["total_local_api_placed"], "undefined_source" => $calculatedStatisticsPerAggregation["total_undefined_placed"]], "contains" => ["product" => $calculatedStatisticsPerAggregation["contains_product"], "addon" => $calculatedStatisticsPerAggregation["contains_addon"], "domain_renewal" => $calculatedStatisticsPerAggregation["contains_domain_renewal"], "domain_transfer" => $calculatedStatisticsPerAggregation["contains_domain_transfer"], "domain_register" => $calculatedStatisticsPerAggregation["contains_domain_register"], "product_addon" => $calculatedStatisticsPerAggregation["contains_product_addon"], "upgrade" => $calculatedStatisticsPerAggregation["contains_upgrade"], "marketconnect" => $calculatedStatisticsPerAggregation["contains_marketconnect_items"], "marketconnect_upgrade" => $calculatedStatisticsPerAggregation["contains_marketconnect_upgrade"], "upsell" => $calculatedStatisticsPerAggregation["contains_upsell"], "cross_sell" => $calculatedStatisticsPerAggregation["contains_cross_sell"], "product_type_hosting" => $calculatedStatisticsPerAggregation["contains_hosting_product_type"], "product_type_reseller" => $calculatedStatisticsPerAggregation["contains_reseller_product_type"], "product_type_server" => $calculatedStatisticsPerAggregation["contains_server_product_type"], "product_type_other" => $calculatedStatisticsPerAggregation["contains_other_product_type"]], "contains_only" => ["product" => $calculatedStatisticsPerAggregation["contains_only_product"], "addon" => $calculatedStatisticsPerAggregation["contains_only_addon"], "domain_renewal" => $calculatedStatisticsPerAggregation["contains_only_domain_renewal"], "domain_transfer" => $calculatedStatisticsPerAggregation["contains_only_domain_transfer"], "domain_register" => $calculatedStatisticsPerAggregation["contains_only_domain_register"], "product_addon" => $calculatedStatisticsPerAggregation["contains_only_product_addon"], "product_domain" => $calculatedStatisticsPerAggregation["contains_only_product_domain"], "product_addon_domain" => $calculatedStatisticsPerAggregation["contains_only_product_addon_domain"], "upgrade" => $calculatedStatisticsPerAggregation["contains_only_upgrade"], "marketconnect" => $calculatedStatisticsPerAggregation["contains_only_marketconnect_items"], "marketconnect_upgrade" => $calculatedStatisticsPerAggregation["contains_only_marketconnect_upgrade"], "product_type_hosting" => $calculatedStatisticsPerAggregation["contains_only_hosting_product_type"], "product_type_reseller" => $calculatedStatisticsPerAggregation["contains_only_reseller_product_type"], "product_type_server" => $calculatedStatisticsPerAggregation["contains_only_server_product_type"], "product_type_other" => $calculatedStatisticsPerAggregation["contains_only_other_product_type"]], "with_promo_code" => $calculatedStatisticsPerAggregation["with_promo_code"], "bundle" => $calculatedStatisticsPerAggregation["bundle"]];
    }
    private function getOrdersStatisticsQueryBuilder($lastDays) : \Illuminate\Database\Eloquent\Builder
    {
        return Order::daysAgo($lastDays)->select(["tblorders.id", "tblorders.amount", "tblclients.currency as order_currency_id", "tblorders.promocode", "tblorders.orderdata", "tblorders.purchase_source", "tblorders.has_referral_products", \WHMCS\Database\Capsule::raw("(tblorders.amount / tblcurrencies.rate) as default_currency_amount"), \WHMCS\Database\Capsule::raw(sprintf("(select sum(tblinvoiceitems.amount)\n                     from tblinvoiceitems\n                     where tblorders.invoiceid = tblinvoiceitems.invoiceid and type = '%s'\n                 ) / tblcurrencies.rate as default_currency_promo_amount", \WHMCS\Billing\InvoiceItemInterface::TYPE_HOSTING_PROMOTION))])->withCount(["services", "addons", "domains", "upgrades", "upgrades as marketconnect_service_upgrades_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->join("tblproducts", "tblproducts.id", "=", \WHMCS\Database\Capsule::raw("substring_index(tblupgrades.newvalue, ',', 1)"))->where("tblproducts.servertype", "marketconnect")->where("tblupgrades.type", \WHMCS\Service\Upgrade\Upgrade::TYPE_SERVICE);
        }, "upgrades as marketconnect_addon_upgrades_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->join("tbladdons", "tbladdons.id", "=", \WHMCS\Database\Capsule::raw("substring_index(tblupgrades.newvalue, ',', 1)"))->where("tbladdons.module", "marketconnect")->where("tblupgrades.type", \WHMCS\Service\Upgrade\Upgrade::TYPE_ADDON);
        }, "invoiceItems as domain_renewals_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where("type", \WHMCS\Billing\InvoiceItemInterface::TYPE_DOMAIN);
        }, "domains as domain_transfers_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where("type", "Transfer");
        }, "domains as domain_registers_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where("type", "Register");
        }, "services as services_hosting_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid")->where("tblproducts.type", "hostingaccount");
        }, "services as services_reseller_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid")->where("tblproducts.type", "reselleraccount");
        }, "services as services_server_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid")->where("tblproducts.type", "server");
        }, "services as services_other_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid")->where("tblproducts.type", "other");
        }, "services as marketconnect_services_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid")->where("tblproducts.servertype", "marketconnect");
        }, "addons as marketconnect_addons_count" => function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->join("tbladdons", "tbladdons.id", "=", "tblhostingaddons.addonid")->where("tbladdons.module", "marketconnect");
        }])->leftJoin("tblinvoices", "tblinvoices.id", "=", "tblorders.invoiceid")->join("tblclients", "tblclients.id", "=", "tblorders.userid")->join("tblcurrencies", "tblcurrencies.id", "=", "tblclients.currency")->where(function (\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where("tblinvoices.status", \WHMCS\Utility\Status::PAID)->orWhere(function (\Illuminate\Database\Eloquent\Builder $freeOrdersCriteriaQuery) {
                return $freeOrdersCriteriaQuery->where("tblorders.amount", 0)->whereIn("tblorders.status", [\WHMCS\Utility\Status::ACTIVE, \WHMCS\Utility\Status::PENDING]);
            });
        });
    }
    private function getOrdersAggregationCriteria() : array
    {
        return ["total_admin_placed" => function ($orderStatisticItem) {
            return $orderStatisticItem->purchase_source === OrderPurchaseSource::ADMIN;
        }, "total_customer_portal_placed" => function ($orderStatisticItem) {
            return $orderStatisticItem->purchase_source === OrderPurchaseSource::CLIENT;
        }, "total_admin_masquerading_placed" => function ($orderStatisticItem) {
            return $orderStatisticItem->purchase_source === OrderPurchaseSource::ADMIN_MASQUERADING_AS_CLIENT;
        }, "total_client_api_placed" => function ($orderStatisticItem) {
            return $orderStatisticItem->purchase_source === OrderPurchaseSource::CLIENT_API;
        }, "total_local_api_placed" => function ($orderStatisticItem) {
            return $orderStatisticItem->purchase_source === OrderPurchaseSource::LOCAL_API;
        }, "total_undefined_placed" => function ($orderStatisticItem) {
            return $orderStatisticItem->purchase_source === OrderPurchaseSource::UNDEFINED;
        }, "contains_product" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_count;
        }, "contains_addon" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->addons_count;
        }, "contains_domain_renewal" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->domain_renewals_count;
        }, "contains_domain_register" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->domain_registers_count;
        }, "contains_domain_transfer" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->domain_transfers_count;
        }, "contains_product_addon" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_count && 0 < $orderStatisticItem->addons_count;
        }, "contains_upgrade" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->upgrades_count;
        }, "contains_marketconnect_items" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->marketconnect_items_count;
        }, "contains_marketconnect_upgrade" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->marketconnect_upgrade_items_count;
        }, "contains_upsell" => function ($orderStatisticItem) {
            $containsReferralProductsMask = new ContainsReferralProductsMask($orderStatisticItem->has_referral_products ?? 0);
            return $containsReferralProductsMask->hasUpsellItems();
        }, "contains_cross_sell" => function ($orderStatisticItem) {
            $containsReferralProductsMask = new ContainsReferralProductsMask($orderStatisticItem->has_referral_products ?? 0);
            return $containsReferralProductsMask->hasRecommendationItems();
        }, "contains_hosting_product_type" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_hosting_count;
        }, "contains_reseller_product_type" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_reseller_count;
        }, "contains_server_product_type" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_server_count;
        }, "contains_other_product_type" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_other_count;
        }, "contains_only_product" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_count && $orderStatisticItem->services_count === $orderStatisticItem->items_count;
        }, "contains_only_addon" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->addons_count && $orderStatisticItem->addons_count === $orderStatisticItem->items_count;
        }, "contains_only_domain_renewal" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->domain_renewals_count && $orderStatisticItem->domain_renewals_count === $orderStatisticItem->items_count;
        }, "contains_only_domain_register" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->domain_registers_count && $orderStatisticItem->domain_registers_count === $orderStatisticItem->items_count;
        }, "contains_only_domain_transfer" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->domain_transfers_count && $orderStatisticItem->domain_transfers_count === $orderStatisticItem->items_count;
        }, "contains_only_product_addon" => function ($orderStatisticItem) {
            $servicesAddonsCount = $orderStatisticItem->services_count + $orderStatisticItem->addons_count;
            return 0 < $orderStatisticItem->services_count && 0 < $orderStatisticItem->addons_count && $servicesAddonsCount === $orderStatisticItem->items_count;
        }, "contains_only_product_domain" => function ($orderStatisticItem) {
            $servicesDomainsCount = $orderStatisticItem->services_count + $orderStatisticItem->domains_count;
            return 0 < $orderStatisticItem->services_count && 0 < $orderStatisticItem->domains_count && $servicesDomainsCount === $orderStatisticItem->items_count;
        }, "contains_only_product_addon_domain" => function ($orderStatisticItem) {
            $servicesAddonsDomainsCount = $orderStatisticItem->services_count + $orderStatisticItem->addons_count + $orderStatisticItem->domains_count;
            return 0 < $orderStatisticItem->services_count && 0 < $orderStatisticItem->addons_count && 0 < $orderStatisticItem->domains_count && $servicesAddonsDomainsCount === $orderStatisticItem->items_count;
        }, "contains_only_upgrade" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->upgrades_count && $orderStatisticItem->upgrades_count === $orderStatisticItem->items_count;
        }, "contains_only_marketconnect_items" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->marketconnect_items_count && $orderStatisticItem->marketconnect_items_count === $orderStatisticItem->items_count;
        }, "contains_only_marketconnect_upgrade" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->marketconnect_upgrade_items_count && $orderStatisticItem->marketconnect_upgrade_items_count === $orderStatisticItem->items_count;
        }, "contains_only_hosting_product_type" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_hosting_count && $orderStatisticItem->services_hosting_count === $orderStatisticItem->items_count;
        }, "contains_only_reseller_product_type" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_reseller_count && $orderStatisticItem->services_reseller_count === $orderStatisticItem->items_count;
        }, "contains_only_server_product_type" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_server_count && $orderStatisticItem->services_server_count === $orderStatisticItem->items_count;
        }, "contains_only_other_product_type" => function ($orderStatisticItem) {
            return 0 < $orderStatisticItem->services_other_count && $orderStatisticItem->services_other_count === $orderStatisticItem->items_count;
        }, "with_promo_code" => function ($orderStatisticItem) {
            return !empty($orderStatisticItem->promocode);
        }, "bundle" => function ($orderStatisticItem) {
            return !empty($orderStatisticItem->orderdata) && strpos($orderStatisticItem->orderdata, "bundleids");
        }];
    }
    private function getOverriddenAggregationCalculators() : array
    {
        return ["with_promo_code" => function (Aggregation\AggregationData $aggregationData) {
            $calculatedStatistics = $this->calculateAggregationStatistics($aggregationData);
            $calculatedStatistics["promo_amount_average"] = $this->currencyRepository->getMoneyAmountsPerCurrency($aggregationData->getPromoAmountAverage());
            return $calculatedStatistics;
        }];
    }
    private function calculateAggregationStatistics(Aggregation\AggregationData $aggregationData) : array
    {
        $averageValuePerCurrency = $this->currencyRepository->getMoneyAmountsPerCurrency($aggregationData->getPriceAverage());
        return ["count" => $aggregationData->getCount(), "value_average" => $averageValuePerCurrency, "items_count_average" => $aggregationData->getItemsCountAverage(), "services_count_average" => $aggregationData->getServicesCountAverage(), "addons_count_average" => $aggregationData->getAddonsCountAverage(), "domains_count_average" => $aggregationData->getDomainsCountAverage(), "marketconnect_items_count_average" => $aggregationData->getMarketConnectItemsCountAverage(), "hosting_product_type_orders_percentage" => $aggregationData->getHostingOrdersPercentage(), "reseller_product_type_orders_percentage" => $aggregationData->getResellerOrdersPercentage(), "server_product_type_orders_percentage" => $aggregationData->getServerOrdersPercentage(), "other_product_type_orders_percentage" => $aggregationData->getOtherOrdersPercentage(), "marketconnect_item_orders_percentage" => $aggregationData->getMarketConnectItemOrdersPercentage(), "product_addon_orders_percentage" => $aggregationData->getServiceAddonOrdersPercentage(), "product_domain_orders_percentage" => $aggregationData->getServiceDomainOrdersPercentage(), "product_addon_domain_orders_percentage" => $aggregationData->getServiceAddonDomainOrdersPercentage()];
    }
}

?>