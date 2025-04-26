<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Invoice\Item;

class ModuleDelegate extends AbstractUsageItem
{
    public function getUsageCalculations(\WHMCS\UsageBilling\Service\ServiceMetric $serviceMetric, \WHMCS\UsageBilling\Contracts\Pricing\PricingSchemaInterface $pricingSchema)
    {
        return [];
    }
    public function getInvoiceItem()
    {
        $attributes = $this->getDefaultServiceAttributes();
        $serviceMetric = $this->getServiceMetric();
        $serviceName = $this->getServiceName();
        $service = $serviceMetric->service();
        $metricName = $serviceMetric->displayName();
        $usageAmount = $serviceMetric->usage()->value();
        $price = NULL;
        $description = "";
        $module = $this->getModule();
        $surchargeCalculation = $module->call("metric_price_calculation", ["metricUsage" => $serviceMetric, "service" => $service, "serviceName" => $serviceName, "usageAmount" => $usageAmount, "metricName" => $metricName]);
        if(isset($surchargeCalculation["price"])) {
            $price = $surchargeCalculation["price"];
        }
        if($price instanceof \WHMCS\View\Formatter\Price) {
            $price = $price->toFull();
        }
        if(!$price) {
            $price = 0;
        }
        if(isset($surchargeCalculation["description"])) {
            $description = $surchargeCalculation["description"];
        }
        if(!$description) {
            $description = $usageAmount . " " . $metricName . " @ " . $price;
        }
        $attributes["description"] = $description;
        $attributes["price"] = $price;
        return new \WHMCS\Billing\Invoice\Item($attributes);
    }
}

?>