<?php

namespace WHMCS\Service;

class DomainOnDemandRenewal implements ServiceOnDemandRenewalInterface
{
    protected $domain;
    public function __construct(\WHMCS\Domain\Domain $domain)
    {
        $this->domain = $domain;
    }
    public static function trackRenewalAddedToCart()
    {
        ServiceOnDemandRenewal::trackRenewalAddedToCartByType("domains");
    }
    public static function trackRenewalCheckedOut()
    {
        ServiceOnDemandRenewal::trackRenewalCheckedOutByType("domains");
    }
    public function getReason()
    {
        throw new \Exception("Not yet implemented.");
    }
    public function isRenewable()
    {
        throw new \Exception("Not yet implemented.");
    }
    public function renew($amount, string $paymentMethod) : \WHMCS\Billing\Invoice\Item
    {
        throw new \Exception("Not yet implemented.");
    }
    public function getBillingCycle()
    {
        throw new \Exception("Not yet implemented.");
    }
    public function getNextPayUntilDate() : \Carbon\CarbonInterface
    {
        throw new \Exception("Not yet implemented.");
    }
    public function getPrice() : \WHMCS\View\Formatter\Price
    {
        throw new \Exception("Not yet implemented.");
    }
    public function getService()
    {
        throw new \Exception("Not yet implemented.");
    }
    public function getProduct() : \WHMCS\Model\AbstractModel
    {
        throw new \Exception("Not yet implemented.");
    }
    public function isTaxable()
    {
        throw new \Exception("Not yet implemented.");
    }
    public function getServiceId() : int
    {
        throw new \Exception("Not yet implemented.");
    }
    public function getProductName()
    {
        throw new \Exception("Not yet implemented.");
    }
}

?>