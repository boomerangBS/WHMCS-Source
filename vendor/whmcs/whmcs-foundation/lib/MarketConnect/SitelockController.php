<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\MarketConnect;

class SitelockController extends AbstractController
{
    protected $serviceName = MarketConnect::SERVICE_SITELOCK;
    protected $langPrefix = MarketConnect::SERVICE_SITELOCK;
    public function index(\WHMCS\Http\Message\ServerRequest $request)
    {
        $ca = parent::index($request);
        if($ca instanceof \WHMCS\Http\RedirectResponse) {
            return $ca;
        }
        $currency = $ca->retrieve("activeCurrency");
        $plans = $ca->retrieve("plans");
        $promoHelper = $ca->retrieve("promoHelper");
        $litePlan = $plans->where("configoption1", Promotion\Service\Sitelock::SITELOCK_LITE)->first();
        $emergencyPlan = $plans->where("configoption1", Promotion\Service\Sitelock::SITELOCK_EMERGENCY)->first();
        if($emergencyPlan) {
            $emergencyPlan->pricing($currency);
        }
        foreach ($plans as $key => $plan) {
            if(in_array($plan->configoption1, Promotion\Service\Sitelock::SITELOCK_SPECIAL)) {
                unset($plans[$key]);
            } else {
                $plan->features = $promoHelper->getPlanFeatures($plan->configoption1);
                $plan->pricing($currency);
            }
        }
        $ca->assign("plans", $plans);
        $ca->assign("litePlan", $litePlan);
        $ca->assign("emergencyPlan", $emergencyPlan);
        $ca->assign("learnMoreLink", "<a href='https://vimeo.com/164301190' target='_blank'>" . \Lang::trans("store.sitelock.faqOneBodyLearnLinkText") . "</a>");
        return $ca;
    }
}

?>