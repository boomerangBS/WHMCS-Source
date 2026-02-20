<?php


namespace WHMCS\ClientArea;
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F436C69656E74417265612F55706772616465436F6E74726F6C6C65722E7068703078376664353934323461643630_
{
    public $cycles;
    protected $currentProductId;
    protected $currentServiceBillingCycle;
    public function __construct($service)
    {
        if($service instanceof WHMCS\Service\Service) {
            $this->currentProductId = $service->product->id;
        } elseif($service instanceof WHMCS\Service\Addon) {
            $this->currentProductId = $service->productAddon->id;
        }
        $this->currentServiceBillingCycle = strtolower($service->billingCycle);
        if((new WHMCS\Billing\Cycles())->isRecurring($service->billingCycle)) {
            $this->cycles = (new WHMCS\Billing\Cycles())->getGreaterCycles($service->billingCycle);
        }
    }
    public function showCycleForProduct($cycle)
    {
        return is_null($this->cycles) || in_array($cycle, $this->cycles);
    }
    public function isCycleDisabledForProduct($productId, string $cycle) : int
    {
        return $productId == $this->currentProductId && $cycle != $this->currentServiceBillingCycle;
    }
}
class UpgradeController
{
    private function renderUpgradePage(\WHMCS\Http\Message\ServerRequest $request, array $extraVars = [])
    {
        $isProduct = (bool) (int) $request->get("isproduct");
        $serviceId = (int) $request->get("serviceid");
        \Auth::requireLoginAndClient(true);
        if(empty($serviceId)) {
            $redirectPath = \WHMCS\Utility\Environment\WebHelper::getBaseUrl() . "/clientarea.php";
            return new \Laminas\Diactoros\Response\RedirectResponse($redirectPath);
        }
        $view = new \WHMCS\ClientArea();
        $view->setTemplate("upgrade-configure");
        $view->addOutputHookFunction("Upgrade");
        $view->setPageTitle(\Lang::trans("upgrade"));
        $view->setDisplayTitle(\Lang::trans("upgrade"));
        $view->addToBreadCrumb("index.php", \Lang::trans("globalsystemname"));
        $view->addToBreadCrumb("clientarea.php", \Lang::trans("clientareatitle"));
        $view->addToBreadCrumb("#", \Lang::trans("upgrade"));
        $currency = \Currency::factoryForClientArea();
        try {
            if($isProduct) {
                $service = \Auth::client()->services()->where("id", $serviceId)->first();
                $module = $service->product->module;
                $marketConnectType = $service->product->serviceKey;
                $allowMultipleQuantities = $service->product->allowMultipleQuantities === 2;
            } else {
                $service = \Auth::client()->addons()->where("id", $serviceId)->first();
                $module = $service->productAddon->module;
                $marketConnectType = \WHMCS\MarketConnect\MarketConnect::getVendorSystemName($service->productAddon->serviceKey);
                $allowMultipleQuantities = $service->productAddon->allowMultipleQuantities === 2;
            }
            if(is_null($service)) {
                throw new \WHMCS\Exception("Invalid link followed. Please go back and try again.");
            }
            if($module != "marketconnect") {
                throw new \WHMCS\Exception("Only MarketConnect services can be upgraded");
            }
            if(!$service->canBeUpgraded()) {
                throw new \WHMCS\Exception("Service not eligible for upgrade");
            }
            if($service instanceof \WHMCS\Service\Service) {
                $productSystemName = \WHMCS\MarketConnect\MarketConnect::getVendorSystemName($marketConnectType);
                $upgradeProducts = \WHMCS\Product\Product::$productSystemName()->visible()->orderBy("order")->get();
                $currentProductKey = $service->product->productKey;
            } elseif($service instanceof \WHMCS\Service\Addon) {
                $addonIds = \WHMCS\Config\Module\ModuleConfiguration::with("productAddon")->where("entity_type", "addon")->where("setting_name", "configoption1")->where("value", "LIKE", $marketConnectType . "\\_%")->get()->pluck("productAddon.id");
                $upgradeProducts = \WHMCS\Product\Addon::marketConnect()->whereIn("id", $addonIds)->get();
                $currentProductKey = $service->productAddon->productKey;
            } else {
                throw new \WHMCS\Exception("Unrecognised service type");
            }
            if(!\WHMCS\MarketConnect\Provision::factoryFromModel($service)->isEligibleForUpgrade()) {
                throw new \WHMCS\Exception("Product not eligible for upgrade");
            }
            $minimumQuantity = $this->getMinimumQuantity($allowMultipleQuantities, $service);
            $promoHelper = \WHMCS\MarketConnect\MarketConnect::factoryPromotionalHelper($marketConnectType);
            foreach ($upgradeProducts as $key => $product) {
                $product->features = $promoHelper->getFeaturesForUpgrade($product->productKey);
                if(is_null($product->features)) {
                    unset($upgradeProducts[$key]);
                } else {
                    $product->pricing($currency);
                    if($service instanceof \WHMCS\Service\Service) {
                        $product->eligibleForUpgrade = $service->product->displayOrder <= $product->displayOrder;
                    } elseif($service instanceof \WHMCS\Service\Addon) {
                        $product->eligibleForUpgrade = $service->productAddon->weight <= $product->weight;
                    }
                }
            }
            $permittedBillingCycles = $this->permittedBillingCycles($service);
            if(is_array($permittedBillingCycles->cycles)) {
                $validUpgradeProducts = [];
                foreach ($upgradeProducts as $key => $product) {
                    $hasCycles = false;
                    foreach ($product->pricing()->allAvailableCycles() as $cycle) {
                        if(in_array($cycle->cycle(), $permittedBillingCycles->cycles)) {
                            $hasCycles = true;
                            if($hasCycles) {
                                $validUpgradeProducts[] = $product;
                            }
                        }
                    }
                }
                $upgradeProducts = $validUpgradeProducts;
            }
            $data = ["errorMessage" => NULL, "isService" => $service instanceof \WHMCS\Service\Service, "isAddon" => $service instanceof \WHMCS\Service\Addon, "upgradeProducts" => $upgradeProducts, "serviceToBeUpgraded" => $service, "recommendedProductKey" => $promoHelper->getRecommendedProductKeyForUpgrade($currentProductKey), "permittedBillingCycles" => $permittedBillingCycles, "allowMultipleQuantities" => $allowMultipleQuantities, "currentQuantity" => $service->qty, "minimumQuantity" => $minimumQuantity];
        } catch (\Exception $e) {
            $data = ["errorMessage" => $e->getMessage()];
        }
        $view->setTemplateVariables(array_merge($data, $extraVars));
        return $view;
    }
    public function index(\WHMCS\Http\Message\ServerRequest $request)
    {
        return $this->renderUpgradePage($request);
    }
    public function addToCart(\WHMCS\Http\Message\ServerRequest $request)
    {
        $isService = (int) $request->request()->get("isproduct");
        $serviceId = (int) $request->request()->get("serviceid");
        $productId = (int) $request->request()->get("productid");
        $billingCycle = $request->request()->get("billingcycle");
        $qty = (int) $request->request()->get("qty", 1);
        if($isService) {
            $service = \Auth::client()->services()->findOrFail($serviceId);
            $currentProduct = $service->product;
            $upgradeProduct = \WHMCS\Product\Product::findOrFail($productId);
        } else {
            $service = \Auth::client()->addons()->findOrFail($serviceId);
            $currentProduct = $service->productAddon;
            $upgradeProduct = \WHMCS\Product\Addon::findOrFail($productId);
        }
        $allowMultipleQuantities = $upgradeProduct->allowMultipleQuantities === 2;
        $eligibleForUpgrade = true;
        if($service instanceof \WHMCS\Service\Service) {
            $eligibleForUpgrade = $service->product->displayOrder <= $upgradeProduct->displayOrder;
        } elseif($service instanceof \WHMCS\Service\Addon) {
            $eligibleForUpgrade = $service->productAddon->weight <= $upgradeProduct->weight;
        }
        $cycles = new \WHMCS\Billing\Cycles();
        if($eligibleForUpgrade) {
            if($cycles->isRecurring($service->billingCycle)) {
                $permittedBillingCycles = (new \WHMCS\Billing\Cycles())->getGreaterCycles($service->billingCycle);
            } else {
                $permittedBillingCycles = [];
            }
            $currentIsFree = $cycles->isFree($service->billingCycle);
            if(!in_array($billingCycle, $permittedBillingCycles) && !$currentIsFree) {
                $eligibleForUpgrade = false;
            }
        }
        if(!$service->canBeUpgraded()) {
            throw new \WHMCS\Exception("Service not eligible for upgrade");
        }
        if(!\WHMCS\MarketConnect\Provision::factoryFromModel($service)->isEligibleForUpgrade()) {
            throw new \WHMCS\Exception("Product not eligible for upgrade");
        }
        if(!$eligibleForUpgrade) {
            return $this->renderUpgradePage($request, ["errorMessage" => \Lang::trans("orderForm.downgradeNotPossible")]);
        }
        if($currentProduct->id === $upgradeProduct->id && $service->qty === $qty && strtolower($service->billingCycle) !== strtolower($billingCycle)) {
            return $this->renderUpgradePage($request, ["errorMessage" => \Lang::trans("orderForm.upgradeQuantityMustChange")]);
        }
        if(!$currentProduct->isValidForUpgrade($upgradeProduct)) {
            throw new \WHMCS\Exception("Not a valid upgrade scenario");
        }
        $minimumQuantity = $this->getMinimumQuantity($allowMultipleQuantities, $service);
        if(!$service->isRecurring()) {
        } else {
            $cyclesHelper = new \WHMCS\Billing\Cycles();
            $monthsAfter = $cyclesHelper->getNumberOfMonths($billingCycle);
            $monthsBefore = $cyclesHelper->getNumberOfMonths($service->billingCycle);
            if($monthsAfter < $monthsBefore) {
                throw new \WHMCS\Exception("Upgrades may only be performed to the same or greater billing cycle term");
            }
            if($currentProduct->id === $upgradeProduct->id && $monthsAfter === $monthsBefore && ($service->qty === $qty || $qty < $minimumQuantity)) {
                if($allowMultipleQuantities && $qty < $minimumQuantity) {
                    return $this->renderUpgradePage($request, ["errorMessage" => \Lang::trans("orderForm.upgradeQuantityCannotBeLowerThanMinimum", [":minimum" => $minimumQuantity])]);
                }
                if($allowMultipleQuantities && $qty === $service->qty) {
                    return $this->renderUpgradePage($request, ["errorMessage" => \Lang::trans("orderForm.upgradeQuantityMustChange", [":current" => $service->qty])]);
                }
                if(!$allowMultipleQuantities) {
                    return $this->renderUpgradePage($request, ["errorMessage" => \Lang::trans("upgradeSameProductMustExtendCycle")]);
                }
            } elseif($allowMultipleQuantities && $qty < $minimumQuantity) {
                return $this->renderUpgradePage($request, ["errorMessage" => \Lang::trans("orderForm.upgradeQuantityCannotBeLowerThanMinimum", [":minimum" => $minimumQuantity])]);
            }
        }
        \WHMCS\OrderForm::addUpgradeToCart($service instanceof \WHMCS\Service\Service ? "service" : "addon", $service->id, $upgradeProduct->id, $billingCycle, $qty, $minimumQuantity);
        $redirectPath = \WHMCS\Utility\Environment\WebHelper::getBaseUrl() . "/cart.php?a=view";
        return new \Laminas\Diactoros\Response\RedirectResponse($redirectPath);
    }
    protected function getMinimumQuantity($allowMultipleQuantities, $service) : int
    {
        if($allowMultipleQuantities) {
            try {
                $minimumQuantity = $service->moduleInterface()->call("getUsedQuantity");
                if(is_numeric($minimumQuantity) && 0 < $minimumQuantity) {
                    return $minimumQuantity;
                }
            } catch (\Exception $e) {
            }
        }
        return 1;
    }
    protected function permittedBillingCycles($service)
    {
        return new func_num_args($service);
    }
}

?>