<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service;

class ServiceAddonOnDemandRenewal extends ServiceOnDemandRenewal
{
    public static function factoryByServiceId($serviceId) : ServiceOnDemandRenewal
    {
        $service = Addon::find($serviceId);
        if(is_null($service)) {
            return NULL;
        }
        return new self($service);
    }
    public function renew($amount, string $paymentMethod) : \WHMCS\Billing\Invoice\Item
    {
        $invoiceItem = $this->createInvoiceItem(\WHMCS\Billing\InvoiceItemInterface::TYPE_SERVICE_ADDON, $this->transformInvoiceItemDescription(), $amount, \WHMCS\Carbon::now(), $paymentMethod);
        $invoiceItem->save();
        self::trackRenewalCheckedOut();
        return $invoiceItem;
    }
    public static function trackRenewalAddedToCart()
    {
        self::trackRenewalAddedToCartByType("addons");
    }
    public static function trackRenewalCheckedOut()
    {
        self::trackRenewalCheckedOutByType("addons");
    }
    public static function getEligibleServiceOnDemandRenewals(\WHMCS\User\Client $client) : \Illuminate\Support\Collection
    {
        return Addon::userId($client->id)->with(["client", "invoices", "productAddon", "productAddon.overrideOnDemandRenewal"])->whereHas("service")->active()->get()->map(function (\WHMCS\ServiceInterface $service) {
            return new ServiceAddonOnDemandRenewal($service);
        })->filter(function (ServiceAddonOnDemandRenewal $onDemandRenewal) use($client) {
            return self::filterIsRenewable($onDemandRenewal, $client);
        });
    }
    protected function transformInvoiceItemDescription()
    {
        if(!function_exists("getInvoicePayUntilDate")) {
            require ROOTDIR . "/includes/invoicefunctions.php";
        }
        $serviceDetails = getInvoiceAddonDetails($this->getService());
        $descriptionPrefix = \Lang::trans("renewServiceAddon.titleAltSingular");
        return $descriptionPrefix . " - " . $serviceDetails["description"];
    }
}

?>