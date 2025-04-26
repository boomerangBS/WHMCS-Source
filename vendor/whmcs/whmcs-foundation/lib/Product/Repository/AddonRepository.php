<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product\Repository;

class AddonRepository
{
    private $currencyRepository;
    public function __construct(\WHMCS\Product\CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }
    public function generateAddonStats() : array
    {
        try {
            $hostingAddonsStats = \WHMCS\Database\Capsule::table("tblhostingaddons")->selectRaw("\n                    tblhostingaddons.billingcycle as billing_cycle,\n                    tbladdons.module as module,\n                    SUM(tblhostingaddons.recurring / tblcurrencies.rate) as default_currency_revenue,\n                    COUNT(tblhostingaddons.id) as count_active,\n                    tblproducts.type as product_type")->join("tbladdons", "tbladdons.id", "=", "tblhostingaddons.addonid")->join("tblclients", "tblclients.id", "=", "tblhostingaddons.userid")->join("tblcurrencies", "tblcurrencies.id", "=", "tblclients.currency")->join("tblorders", "tblorders.id", "=", "tblhostingaddons.orderid")->join("tblhosting", "tblhosting.id", "=", "tblhostingaddons.hostingid")->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid")->where("tblhostingaddons.status", "=", "Active")->groupBy("tblhostingaddons.billingcycle", "tbladdons.module", "tblproducts.type")->get()->toArray();
            $groupedData = [];
            foreach ($hostingAddonsStats as $piece) {
                if(!isset($groupedData[$piece->module])) {
                    $groupedData[$piece->module] = [];
                }
                $groupedData[$piece->module][] = ["module" => $piece->module, "product_type" => $piece->product_type, "billing_cycle" => $piece->billing_cycle, "count_active" => $piece->count_active, "revenue" => $this->currencyRepository->getMoneyAmountsPerCurrency((double) $piece->default_currency_revenue)];
            }
            return $groupedData;
        } catch (\Throwable $e) {
            return [];
        }
    }
}

?>