<?php

namespace WHMCS\MarketConnect\Promotion\Service;

class NordVPN extends AbstractService
{
    protected $name = \WHMCS\MarketConnect\MarketConnect::SERVICE_NORDVPN;
    protected $friendlyName = "NordVPN";
    protected $primaryIcon = "assets/img/marketconnect/nordvpn/logo.png";
    protected $promosRequireQualifyingProducts = false;
    protected $requiresDomain = false;
    protected $productKeys;
    protected $qualifyingProductTypes;
    protected $loginPanel;
    protected $defaultPromotionalContent;
    protected $promotionalContent;
    protected $planFeatures;
    const NORDVPN_STANDARD = NULL;
    public function getPlanFeatures($key)
    {
        return isset($this->planFeatures[$key]) ? $this->planFeatures[$key] : [];
    }
}

?>