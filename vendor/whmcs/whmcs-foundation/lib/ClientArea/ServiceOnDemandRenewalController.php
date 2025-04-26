<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\ClientArea;

// Decoded file for php version 72.
class ServiceOnDemandRenewalController
{
    public function showServices(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\ClientArea
    {
        \Auth::requireLoginAndClient(true);
        $client = \Auth::client();
        $renewData = $client->getOnDemandRenewalServices();
        $renewalsData = [];
        $renewData->each(function (\WHMCS\Service\ServiceOnDemandRenewalInterface $serviceRenewal) use($renewalsData) {
            $renewalsData[] = $this->serviceOnDemandWithAddonsToArray($serviceRenewal);
        });
        $renewalItemCount = count($renewalsData);
        foreach ($renewalsData as $renewalsDatum) {
            if(!empty($renewalsDatum["addons"])) {
                $renewalItemCount += count($renewalsDatum["addons"]);
            }
        }
        return $this->renderServiceRenewals($renewalsData, $renewalItemCount);
    }
    public function showService(\WHMCS\Http\Message\ServerRequest $request)
    {
        \Auth::requireLoginAndClient(true);
        $serviceId = (int) $request->get("serviceid");
        if(empty($serviceId)) {
            return $this->clientAreaRedirectResponse();
        }
        $client = \Auth::client();
        $renewalsData = [];
        try {
            $service = \WHMCS\Service\Service::userId($client->id)->find($serviceId);
            if(is_null($service)) {
                throw new \Exception("Invalid link. Go back and try again.");
            }
            $serviceRenewal = new \WHMCS\Service\ServiceOnDemandRenewal($service);
            if(!$serviceRenewal->isRenewable()) {
                throw new \Exception("This service is not eligible for on-demand renewal.");
            }
            $renewalsData[] = $this->serviceOnDemandWithAddonsToArray($serviceRenewal);
        } catch (\Exception $e) {
            \App::redirect("clientarea.php", ["action" => "productdetails", "id" => $serviceId]);
        }
        $renewalItemCount = 0;
        foreach ($client->getOnDemandRenewalServices() as $renewalService) {
            $renewalItemCount++;
            $renewalItemCount += count($this->getRenewableServiceAddons($renewalService->getService()));
        }
        return $this->renderServiceRenewals($renewalsData, $renewalItemCount);
    }
    protected function renderServiceRenewals($renewData, int $totalServices) : \WHMCS\ClientArea
    {
        $orderForm = new \WHMCS\OrderForm();
        $orderFormTemplate = \WHMCS\View\Template\OrderForm::factory();
        $orderFormTemplateName = $orderFormTemplate->getName();
        $gid = "service-renewals";
        try {
            \WHMCS\View\Template\OrderForm::factory("service-renewals.tpl", $orderFormTemplateName);
        } catch (\WHMCS\Exception\View\TemplateNotFound $e) {
            \App::redirect(\App::getSystemURL() . "cart.php", ["gid" => $gid]);
        }
        $currency = \Currency::factoryForClientArea();
        $templateVariables["currency"] = $currency;
        $templateVariables["ipaddress"] = \App::getRemoteIp();
        $templateVariables["inShoppingCart"] = true;
        $templateVariables["action"] = "add";
        $templateVariables["numitemsincart"] = $orderForm->getNumItemsInCart();
        $templateVariables["gid"] = $gid;
        $templateVariables["services"] = "renew";
        $templateVariables["carttpl"] = $orderFormTemplateName;
        $templateVariables["showSidebarToggle"] = (bool) \WHMCS\Config\Setting::getValue("OrderFormSidebarToggle");
        $view = new \WHMCS\ClientArea();
        $view->initPage();
        $view->setTemplate("service-renewals");
        $view->setPageTitle(\Lang::trans("servicesRenew"));
        $view->addToBreadCrumb("index.php", \Lang::trans("globalsystemname"))->addToBreadCrumb("clientarea.php", \Lang::trans("clientareatitle"))->addToBreadCrumb("clientarea.php?action=services", \Lang::trans("clientareanavservices"))->addToBreadCrumb(routePath("service-renewals"), \Lang::trans("servicesRenew"));
        $renewalItemCount = count($renewData);
        foreach ($renewData as $renewDatum) {
            if(!empty($renewDatum["addons"])) {
                $renewalItemCount += count($renewDatum["addons"]);
            }
        }
        $templateVariables["renewableServices"] = $renewData;
        $templateVariables["totalResults"] = $renewalItemCount;
        $templateVariables["totalServiceCount"] = $totalServices;
        $view->setTemplateVariables($templateVariables);
        \Menu::addContext("productGroups", $orderForm->getProductGroups(true));
        \Menu::addContext("productGroupId", $templateVariables["gid"]);
        \Menu::addContext("domainRegistrationEnabled", (bool) \WHMCS\Config\Setting::getValue("AllowRegister"));
        \Menu::addContext("domainTransferEnabled", (bool) \WHMCS\Config\Setting::getValue("AllowTransfer"));
        \Menu::addContext("domainRenewalEnabled", (bool) \WHMCS\Config\Setting::getValue("EnableDomainRenewalOrders"));
        \Menu::addContext("currency", $templateVariables["currency"]);
        \Menu::addContext("action", $templateVariables["action"]);
        \Menu::addContext("allowRemoteAuth", true);
        \Menu::primarySidebar("orderFormView");
        \Menu::secondarySidebar("orderFormView");
        $view->isInOrderForm();
        return $view;
    }
    public function addRenewal(\WHMCS\Http\Message\ServerRequest $request)
    {
        check_token();
        $serviceId = (int) $request->get("serviceid");
        $service = \WHMCS\Service\Service::userId(\Auth::client()->id)->where("id", $serviceId)->first();
        if(empty($serviceId) || is_null($service)) {
            return $this->clientAreaRedirectResponse();
        }
        $onDemandService = new \WHMCS\Service\ServiceOnDemandRenewal($service);
        if(!$onDemandService->isRenewable()) {
            return $this->clientAreaRedirectResponse();
        }
        \WHMCS\OrderForm::addServiceRenewalToCart($onDemandService);
        return new \WHMCS\Http\Message\JsonResponse(["result" => "added"]);
    }
    public function addAddonRenewal(\WHMCS\Http\Message\ServerRequest $request)
    {
        check_token();
        $serviceAddonId = (int) $request->get("addonid");
        $service = \WHMCS\Service\Addon::userId(\Auth::client()->id)->where("id", $serviceAddonId)->first();
        if(empty($serviceAddonId) || is_null($service)) {
            return $this->clientAreaRedirectResponse();
        }
        $onDemandService = new \WHMCS\Service\ServiceAddonOnDemandRenewal($service);
        if(!$onDemandService->isRenewable()) {
            return $this->clientAreaRedirectResponse();
        }
        \WHMCS\OrderForm::addServiceAddonRenewalToCart($onDemandService);
        return new \WHMCS\Http\Message\JsonResponse(["result" => "added"]);
    }
    public function calcRenewalCartTotals() : \WHMCS\Http\Message\JsonResponse
    {
        if(!function_exists("calcCartTotals")) {
            require ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "orderfunctions.php";
        }
        $currency = \Currency::factoryForClientArea();
        $view = new \WHMCS\ClientArea();
        $orderFormTemplate = \WHMCS\View\Template\OrderForm::factory("ordersummary.tpl");
        $orderFormTemplateName = $orderFormTemplate->getName();
        $orderSummaryTemplate = "/templates/orderforms/" . $orderFormTemplateName . "/ordersummary.tpl";
        $cartTotals = calcCartTotals(\Auth::client(), false, true);
        $renewalsDataReplacements = ["rawtotal" => 0, "subtotal" => 0, "taxtotal" => 0, "taxtotal2" => 0];
        $renewalsDataReplacements = $this->sumTaxes($cartTotals["renewalsByType"]["services"], $renewalsDataReplacements);
        $renewalsDataReplacements = $this->sumTaxes($cartTotals["renewalsByType"]["addons"], $renewalsDataReplacements);
        if(!empty($renewalsDataReplacements["taxtotal"])) {
            $renewalsDataReplacements["taxtotal"] = new \WHMCS\View\Formatter\Price($renewalsDataReplacements["taxtotal"], $currency);
        }
        if($renewalsDataReplacements["taxtotal2"]) {
            $renewalsDataReplacements["taxtotal2"] = new \WHMCS\View\Formatter\Price($renewalsDataReplacements["taxtotal2"], $currency);
        }
        $renewalsDataReplacements["subtotal"] = new \WHMCS\View\Formatter\Price($renewalsDataReplacements["subtotal"], $currency);
        $renewalsDataReplacements["total"] = new \WHMCS\View\Formatter\Price($renewalsDataReplacements["rawtotal"], $currency);
        $cartTotals = array_merge($cartTotals, $renewalsDataReplacements);
        $templateVariables = ["producttotals" => [], "carttotals" => $cartTotals, "serviceRenewals" => true];
        return new \WHMCS\Http\Message\JsonResponse(["body" => $view->getSingleTPLOutput($orderSummaryTemplate, $templateVariables)]);
    }
    protected function clientAreaRedirectResponse() : \Laminas\Diactoros\Response\RedirectResponse
    {
        $redirectPath = \WHMCS\Utility\Environment\WebHelper::getBaseUrl() . "/clientarea.php";
        return new \Laminas\Diactoros\Response\RedirectResponse($redirectPath);
    }
    protected function sumTaxes($services, array $taxes) : array
    {
        foreach ($services as $renewal) {
            $renewalPriceNumeric = $renewal["recurringBeforeTax"]->toNumeric();
            $tax1 = 0;
            if(!empty($renewal["taxes"]["tax1"])) {
                $tax1 = $renewal["taxes"]["tax1"]->toNumeric();
            }
            $taxes["taxtotal"] += $tax1;
            $tax2 = 0;
            if(!empty($renewal["taxes"]["tax2"])) {
                $tax2 = $renewal["taxes"]["tax2"]->toNumeric();
            }
            $taxes["taxtotal2"] += $tax2;
            $taxes["subtotal"] += $renewalPriceNumeric;
            $taxes["rawtotal"] += $renewalPriceNumeric + $tax1 + $tax2;
        }
        return $taxes;
    }
    protected function getRenewableServiceAddons(\WHMCS\Service\Service $service) : array
    {
        $renewalAddons = [];
        $service->addons->each(function (\WHMCS\ServiceInterface $addon) {
            static $renewalAddons = NULL;
            $serviceRenewal = new \WHMCS\Service\ServiceAddonOnDemandRenewal($addon);
            $renewalAddons[] = $this->serviceOnDemandToArray($serviceRenewal);
        });
        return $renewalAddons;
    }
    protected function serviceOnDemandToArray(\WHMCS\Service\ServiceOnDemandRenewal $serviceRenewal) : array
    {
        $serviceIsRecurring = $serviceRenewal->getService()->isRecurring();
        return ["serviceId" => $serviceRenewal->getServiceId(), "product" => $serviceRenewal->getProduct(), "domain" => $serviceRenewal->getService()->getServiceDomain(), "nextDueDate" => $serviceIsRecurring ? $serviceRenewal->getServiceNextDueDate() : NULL, "nextPayUntilDate" => $serviceIsRecurring ? $serviceRenewal->getNextPayUntilDate() : NULL, "billingCycle" => $serviceRenewal->getBillingCycle(), "price" => $serviceRenewal->getPrice()->toFull(), "renewable" => $serviceRenewal->isRenewable(), "reason" => $serviceRenewal->getReason()];
    }
    protected function serviceOnDemandWithAddonsToArray(\WHMCS\Service\ServiceOnDemandRenewal $serviceRenewal) : array
    {
        $onDemandServiceRenewals = $this->serviceOnDemandToArray($serviceRenewal);
        $onDemandAddonRenewals = $this->getRenewableServiceAddons($serviceRenewal->getService());
        $onDemandServiceRenewals["addons"] = $onDemandAddonRenewals;
        $onDemandServiceRenewals["renewableCount"] = collect($onDemandAddonRenewals)->where("renewable", true)->count();
        return $onDemandServiceRenewals;
    }
}

?>