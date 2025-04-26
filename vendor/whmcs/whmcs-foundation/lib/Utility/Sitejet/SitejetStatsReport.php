<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Utility\Sitejet;

class SitejetStatsReport
{
    const ACTIVE_SERVICE_ACTIONS_PAST_DAYS = [30, 90];
    const SERVICE_ORDERS_PAST_DAYS = [30, 90, 0];
    protected function getGroupedEventStatsForPeriod($daysBack, array $actions, $groupByActor) : array
    {
        if(0 < $daysBack) {
            $oldestDate = \WHMCS\Carbon::now()->subDays($daysBack);
        } else {
            $oldestDate = NULL;
        }
        $groupByColumns = ["name"];
        if($groupByActor) {
            $groupByColumns[] = "actor";
        }
        $query = \WHMCS\Service\ServiceData::scope(SitejetStats::SERVICE_DATA_SCOPE)->whereIn("name", $actions)->groupBy($groupByColumns);
        if(!is_null($oldestDate)) {
            $query->where("created_at", ">", $oldestDate);
        }
        $selectActor = $groupByActor ? "actor," : "";
        $selectColumns = $selectActor . "\nname AS action,\nCOUNT(id) AS total_count,\nCOUNT(DISTINCT service_id) AS services_count,\nCOUNT(DISTINCT client_id) AS clients_count";
        return $query->selectRaw($selectColumns)->get()->toArray();
    }
    protected function getActiveServiceActionStats() : array
    {
        $result = [];
        $actions = [SitejetStats::NAME_SSO, SitejetStats::NAME_PUBLISH];
        foreach (self::ACTIVE_SERVICE_ACTIONS_PAST_DAYS as $daysBack) {
            $byActorStats = $this->getGroupedEventStatsForPeriod($daysBack, $actions, true);
            $serviceTotalStats = $this->getGroupedEventStatsForPeriod($daysBack, $actions, false);
            $periodPrefix = 0 < $daysBack ? $daysBack . "_days" : "all_time";
            foreach ($byActorStats as $statItem) {
                foreach (["total", "services", "clients"] as $statSuffix) {
                    $flatLabelFullBreakout = implode("_", [$periodPrefix, $statItem["action"], "by", $statItem["actor"], $statSuffix]);
                    $result[$flatLabelFullBreakout] = ($result[$flatLabelFullBreakout] ?? 0) + $statItem[$statSuffix . "_count"];
                }
            }
            foreach ($serviceTotalStats as $statItem) {
                $flatLabelFullBreakout = implode("_", [$periodPrefix, $statItem["action"], "services"]);
                $result[$flatLabelFullBreakout] = ($result[$flatLabelFullBreakout] ?? 0) + $statItem["services_count"];
            }
        }
        ksort($result);
        return $result;
    }
    protected function getOrderStats() : array
    {
        $result = [];
        foreach (self::SERVICE_ORDERS_PAST_DAYS as $daysBack) {
            $stats = $this->getGroupedEventStatsForPeriod($daysBack, [SitejetStats::NAME_SERVICE_ORDER, SitejetStats::NAME_ADDON_ORDER, SitejetStats::NAME_ADDON_BUNDLE_ORDER, SitejetStats::NAME_SERVICE_UPGRADE], false);
            $periodPrefix = 0 < $daysBack ? $daysBack . "_days" : "all_time";
            foreach ($stats as $statItem) {
                $flatLabel = implode("_", [$periodPrefix, "sales", $statItem["action"]]);
                $result[$flatLabel] = ($result[$flatLabel] ?? 0) + $statItem["total_count"];
            }
        }
        ksort($result);
        return $result;
    }
    protected function getProductAvailabilityStats() : array
    {
        $products = \WHMCS\Product\Product::all();
        $productsWithSitejetPerPanel = $products->filter(function (\WHMCS\Product\Product $product) {
            return \WHMCS\Service\Adapters\SitejetProductAdapter::factory($product)->hasSitejetAvailable();
        })->groupBy("servertype")->keyBy(function ($item, $key) {
            return "products_with_sitejet_" . $key;
        })->map(function (\Illuminate\Support\Collection $collection) {
            return $collection->count();
        })->toArray();
        $addonsWithSitejetPerPanel = collect();
        $productsUpsellableToSitejetPerPanel = collect();
        foreach ($products as $product) {
            $moduleName = $product->module;
            if(!$moduleName) {
            } else {
                $sitejetAddons = \WHMCS\Service\Adapters\SitejetProductAdapter::factory($product)->getAvailableSitejetProductAddons();
                $addonsWithSitejetPerPanel = $addonsWithSitejetPerPanel->mergeRecursive([$moduleName => $sitejetAddons->toArray()]);
                if(!\WHMCS\Service\Adapters\SitejetProductAdapter::factory($product)->hasSitejetAvailable() && 0 < $sitejetAddons->count()) {
                    $productsUpsellableToSitejetPerPanel = $productsUpsellableToSitejetPerPanel->mergeRecursive([$moduleName => [$product]]);
                }
            }
        }
        $addonsWithSitejetPerPanel = $addonsWithSitejetPerPanel->map(function (array $data) {
            return collect($data)->unique("id")->count();
        })->keyBy(function ($item, $key) {
            return "addons_with_sitejet_" . $key;
        })->toArray();
        $productsUpsellableToSitejetPerPanel = $productsUpsellableToSitejetPerPanel->map(function (array $data) {
            return collect($data)->unique("id")->count();
        })->keyBy(function ($item, $key) {
            return "products_with_sitejet_addons_" . $key;
        })->toArray();
        return array_merge($productsWithSitejetPerPanel, $addonsWithSitejetPerPanel, $productsUpsellableToSitejetPerPanel);
    }
    protected function getServiceStatsForServerIds(array $serverIds)
    {
        $serviceCount = \WHMCS\Service\Service::isConsideredActive()->whereIn("server", $serverIds)->count();
        $clientCount = \WHMCS\Service\Service::isConsideredActive()->whereIn("server", $serverIds)->distinct("userid")->count();
        return ["services" => $serviceCount, "clients" => $clientCount];
    }
    protected function getCompatiblePanelServiceStats() : array
    {
        $result = [];
        $compatiblePanelServerIds = \WHMCS\Product\Server\Adapters\SitejetServerAdapter::getCompatibleServers()->groupBy("type")->map(function (\Illuminate\Support\Collection $servers) {
            return $servers->pluck("id")->toArray();
        });
        foreach ($compatiblePanelServerIds as $panel => $serverIds) {
            $panelStats = $this->getServiceStatsForServerIds($serverIds);
            foreach ($panelStats as $statName => $count) {
                $flatLabel = implode("_", [$panel, $statName]);
                $result[$flatLabel] = $count;
            }
        }
        $sitejetPackageServers = \WHMCS\Product\Server\Adapters\SitejetServerAdapter::getServersWithSitejetPackages()->groupBy("type")->map(function (\Illuminate\Support\Collection $servers) {
            return $servers->pluck("id")->toArray();
        });
        foreach ($sitejetPackageServers as $panel => $serverIds) {
            $panelStats = $this->getServiceStatsForServerIds($serverIds);
            foreach ($panelStats as $statName => $count) {
                $flatLabel = implode("_", [$panel, "enabled", "server", $statName]);
                $result[$flatLabel] = $count;
            }
        }
        return $result;
    }
    protected function getEmptyStats() : array
    {
        $keys = ["products_with_sitejet_cpanel", "products_with_sitejet_plesk", "addons_with_sitejet_cpanel", "addons_with_sitejet_plesk", "products_with_sitejet_addons_cpanel", "products_with_sitejet_addons_plesk", "cpanel_services", "cpanel_clients", "plesk_services", "plesk_clients", "cpanel_enabled_server_services", "cpanel_enabled_server_clients", "plesk_enabled_server_services", "plesk_enabled_server_clients", "30_days_sales_addon_bundle_order", "30_days_sales_addon_order", "30_days_sales_service_order", "30_days_sales_upgrade", "90_days_sales_addon_bundle_order", "90_days_sales_addon_order", "90_days_sales_service_order", "90_days_sales_upgrade", "all_time_sales_addon_bundle_order", "all_time_sales_addon_order", "all_time_sales_service_order", "all_time_sales_upgrade", "30_days_publish_by_admin_clients", "30_days_publish_by_admin_services", "30_days_publish_by_admin_total", "30_days_publish_by_client_clients", "30_days_publish_by_client_services", "30_days_publish_by_client_total", "30_days_publish_services", "30_days_sso_by_admin_clients", "30_days_sso_by_admin_services", "30_days_sso_by_admin_total", "30_days_sso_by_client_clients", "30_days_sso_by_client_services", "30_days_sso_by_client_total", "30_days_sso_services", "90_days_publish_by_admin_clients", "90_days_publish_by_admin_services", "90_days_publish_by_admin_total", "90_days_publish_by_client_clients", "90_days_publish_by_client_services", "90_days_publish_by_client_total", "90_days_publish_services", "90_days_sso_by_admin_clients", "90_days_sso_by_admin_services", "90_days_sso_by_admin_total", "90_days_sso_by_client_clients", "90_days_sso_by_client_services", "90_days_sso_by_client_total", "90_days_sso_services"];
        return array_combine($keys, array_fill(0, count($keys), 0));
    }
    public function getStats() : array
    {
        try {
            return array_merge($this->getEmptyStats(), $this->getProductAvailabilityStats(), $this->getCompatiblePanelServiceStats(), $this->getOrderStats(), $this->getActiveServiceActionStats());
        } catch (\Throwable $e) {
            return $this->getEmptyStats();
        }
    }
}

?>