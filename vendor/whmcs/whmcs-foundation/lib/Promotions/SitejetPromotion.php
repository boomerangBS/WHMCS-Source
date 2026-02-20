<?php

namespace WHMCS\Promotions;

class SitejetPromotion extends AbstractPromotion
{
    public function isPromotable()
    {
        $sitejet = new \WHMCS\Utility\Sitejet\SitejetHandler();
        return $sitejet->isSitejetAvailable() && !$sitejet->isSitejetConfigured();
    }
}

?>