<?php

namespace WHMCS\Payment\Observer;

class CancelObsoleteSubscriptions implements ObserverInterface
{
    public function observe(\WHMCS\Payment\Event\PaymentBySupersedingGateway $event) : void
    {
        $hasSubscriptionObsolescenceDisabled = function () {
            return (bool) \DI::make("config")->disable_subscription_obsolescence;
        };
        $getIdentifiers = function ($paymentGateways) {
            return $paymentGateways->map(function (\WHMCS\Billing\Gateway\Contract\PaymentGatewayInterface $paymentGateway) {
                return $paymentGateway->systemIdentifier();
            })->all();
        };
        $getObsoletePaymentGateways = function () {
            return \DI::make("WHMCS\\Billing\\Gateway\\PaymentGatewayServiceProvider")->all()->obsolete();
        };
        $getSupersedingPaymentGateways = function ($obsoleteGateways) {
            $supersedingGateways = NULL;
            $obsoleteGateways->each(function (\WHMCS\Billing\Gateway\Contract\PaymentGatewayInterface $gateway) use($supersedingGateways) {
                if(!$gateway->isSuperseded()) {
                    return NULL;
                }
                if(is_null($supersedingGateways)) {
                    $supersedingGateways = $gateway->supersededBy();
                } else {
                    $supersedingGateways->merge($supersedingGateways);
                }
            });
            return $supersedingGateways;
        };
        $obsoletePaymentGateways = $getObsoletePaymentGateways();
        $obsoleteIdentifiers = $getIdentifiers($obsoletePaymentGateways);
        $supersedingIdentifiers = $getIdentifiers($getSupersedingPaymentGateways($obsoletePaymentGateways));
        $isEventRelatedToObsoleteGateway = function ($event) use($obsoleteIdentifiers) {
            return in_array($event->paymentGateway()->systemIdentifier(), $obsoleteIdentifiers);
        };
        $isEventRelatedToSupersedingGateway = function ($event) use($supersedingIdentifiers) {
            return in_array($event->paymentGateway()->systemIdentifier(), $supersedingIdentifiers);
        };
        if($hasSubscriptionObsolescenceDisabled() || !$isEventRelatedToSupersedingGateway($event) && !$isEventRelatedToObsoleteGateway($event)) {
            return NULL;
        }
        \WHMCS\Scheduling\Jobs\Queue::add("SubscriptionObsolescence", "WHMCS\\Payment\\SubscriptionObsolescenceJob", "manageFromClientId", [$event->invoice()->clientId, $obsoleteIdentifiers, $supersedingIdentifiers]);
    }
}

?>