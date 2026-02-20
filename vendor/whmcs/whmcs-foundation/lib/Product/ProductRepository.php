<?php

namespace WHMCS\Product;

class ProductRepository
{
    private $currencyRepository;
    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }
    public function getProductsSummaryStatistic() : \Illuminate\Support\Collection
    {
        try {
            return $this->getActiveProducts()->values()->map(function (Product $product) {
                $pricingCycles = $product->pricing()->allAvailableCycles();
                $pricing = [];
                $setupFees = [];
                $cycles = [];
                foreach ($pricingCycles as $pricingCycle) {
                    $cycles[] = $pricingCycle->cycle();
                    $pricing[$pricingCycle->cycle()] = $this->currencyRepository->getMoneyAmountsPerCurrency($pricingCycle->price()->getValue());
                    $setup = $pricingCycle->setup();
                    if(!is_null($setup)) {
                        $setupFees[$pricingCycle->cycle()] = $this->currencyRepository->getMoneyAmountsPerCurrency($setup->getValue());
                    }
                }
                return ["name" => $product->name, "group" => $product->productGroup->name, "type" => $product->type, "module" => $product->module, "cycles" => $cycles, "pricing" => $pricing, "setup_fees" => $setupFees];
            })->groupBy("module");
        } catch (\Throwable $e) {
            return collect();
        }
    }
    public function getServiceStatistics() : \Illuminate\Support\Collection
    {
        try {
            $activeProducts = $this->getActiveProducts();
            return \WHMCS\Database\Capsule::table("tblhosting as hosting")->select(["hosting.billingcycle", \WHMCS\Database\Capsule::raw("count(hosting.id) as count_active"), \WHMCS\Database\Capsule::raw("sum(hosting.amount / currencies.rate) as default_currency_revenue"), "products.servertype as module_name", "products.type as product_type"])->join("tblclients as clients", "clients.id", "=", "hosting.userid")->leftJoin("tblcurrencies as currencies", "currencies.id", "=", "clients.currency")->join("tblproducts as products", "products.id", "=", "hosting.packageid")->whereIn("products.id", $activeProducts->keys())->where("hosting.domainstatus", "Active")->groupBy("products.servertype", "products.type", "hosting.billingcycle")->get()->map(function ($item) {
                return ["module" => $item->module_name, "product_type" => $item->product_type, "billing_cycle" => $item->billingcycle, "count_active" => $item->count_active, "revenue" => $this->currencyRepository->getMoneyAmountsPerCurrency((double) $item->default_currency_revenue)];
            })->groupBy("module");
        } catch (\Throwable $e) {
            return collect();
        }
    }
    private function getActiveProducts() : \Illuminate\Support\Collection
    {
        return Product::query()->with(["productGroup"])->where("hidden", 0)->where("retired", 0)->get()->keyBy("id");
    }
}

?>