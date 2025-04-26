<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\MarketConnect\Repository;

class MarketConnectRepository
{
    public function generateMarketConnectStats() : array
    {
        try {
            $allProductsToTheirServices = \WHMCS\MarketConnect\MarketConnect::getProductKeysToServices();
            unset($allProductsToTheirServices[""]);
            $countOfAllServicesProducts = array_count_values($allProductsToTheirServices);
            $servicesWithState = \WHMCS\MarketConnect\MarketConnect::getServicesStateMap();
            $marketConnectSystemStats = [];
            foreach ($servicesWithState as $name => $state) {
                $marketConnectSystemStats[$name] = ["isActive" => $state === true ? 1 : 0, "totalProducts" => $countOfAllServicesProducts[$name] ?? 0, "visibleProducts" => \WHMCS\Database\Capsule::table("tblproducts")->whereIn("configoption1", array_keys($allProductsToTheirServices, $name))->where("hidden", "=", "0")->count()];
            }
            return $marketConnectSystemStats;
        } catch (\Throwable $throwable) {
            return [];
        }
    }
}

?>