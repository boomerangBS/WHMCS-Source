<?php


namespace WHMCS\MarketConnect\Ssl;
class Renew
{
    public $orderNumber;
    public $term;
    public $callbackUrl;
    public $useInstantIssuance = false;
    protected $ssl;
    protected $finalized = false;
    public function __construct(\WHMCS\Service\Ssl $ssl)
    {
        $this->ssl = $ssl;
    }
    public function populate() : \self
    {
        $this->order($this->ssl->getOrderNumber());
        return $this;
    }
    public function order($number) : \self
    {
        $this->orderNumber = $number;
        return $this;
    }
    public function term($number) : \self
    {
        $this->term = $number;
        return $this;
    }
    public function callbackUrl($url) : \self
    {
        $this->callbackUrl = $url;
        return $this;
    }
    public function finalize() : \self
    {
        if($this->isFinalized()) {
            return $this;
        }
        $this->callbackUrl = fqdnRoutePath("store-ssl-callback");
        $this->finalized = true;
        return $this;
    }
    public function isFinalized()
    {
        return $this->finalized;
    }
    public function setUseInstantIssuance($value) : \self
    {
        $this->useInstantIssuance = $value;
        return $this;
    }
}

?>